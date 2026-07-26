<?php
// Razorpay webhook handler
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';
header('Content-Type: application/json');

$cfg = require __DIR__ . '/../../config.php';
$rz = $cfg['razorpay'];
if (empty($rz['key_secret'])) { http_response_code(400); echo json_encode(['error'=>'not_configured']); exit; }

$body = @file_get_contents('php://input');
$sig = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? ($_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '');
if (!$sig) { http_response_code(400); echo json_encode(['error'=>'no_signature']); exit; }

require_once __DIR__ . '/../../vendor/autoload.php';
try {
    $api = new \Razorpay\Api\Api($rz['key_id'], $rz['key_secret']);
    // verify webhook signature
    $api->utility->verifyWebhookSignature($body, $sig, $rz['key_secret']);
    $payload = json_decode($body, true);
    $event = $payload['event'] ?? '';
    if ($event === 'payment.captured' || $event === 'payment.authorized') {
        $pay = $payload['payload']['payment']['entity'] ?? [];
        $payment_id = $pay['id'] ?? null;
        $order_id = $pay['order_id'] ?? null;
        $status = $pay['status'] ?? null;
        // update payments table
        $upd = pdo()->prepare('UPDATE payments SET payment_id=?, status=?, signature=? WHERE order_id=?');
        $upd->execute([$payment_id, 'paid', $sig, $order_id]);
        // find payment -> update fees
        $pstmt = pdo()->prepare('SELECT * FROM payments WHERE order_id=?'); $pstmt->execute([$order_id]); $r = $pstmt->fetch();
        if ($r && $r['fee_id']) {
            $u2 = pdo()->prepare('UPDATE fees SET status=?, paid_on=? WHERE id=?');
            $u2->execute(['paid', date('Y-m-d H:i:s'), $r['fee_id']]);
        }
    }
    echo json_encode(['ok'=>true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error'=>'verify_failed','message'=>$e->getMessage()]);
}
