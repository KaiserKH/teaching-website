<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';
header('Content-Type: application/json');
if (!is_student_logged_in()) { echo json_encode(['error'=>'unauthenticated']); exit; }
$rzCfg = (require __DIR__ . '/../../config.php')['razorpay'];
if (empty($rzCfg['key_id']) || empty($rzCfg['key_secret'])) { echo json_encode(['error'=>'not_configured']); exit; }

$post = $_POST;
$order_id = $post['razorpay_order_id'] ?? '';
$payment_id = $post['razorpay_payment_id'] ?? '';
$signature = $post['razorpay_signature'] ?? '';
$fee_id = intval($post['fee_id'] ?? 0);
if (!$order_id || !$payment_id || !$signature || !$fee_id) { echo json_encode(['error'=>'missing_params']); exit; }

require_once __DIR__ . '/../../vendor/autoload.php';
try {
    $api = new \Razorpay\Api\Api($rzCfg['key_id'], $rzCfg['key_secret']);
    $attributes = ['razorpay_order_id'=>$order_id, 'razorpay_payment_id'=>$payment_id, 'razorpay_signature'=>$signature];
    $api->utility->verifyPaymentSignature($attributes);

    // mark payments table and fees table
    $upd = pdo()->prepare('UPDATE payments SET payment_id=?, signature=?, status=? WHERE order_id=?');
    $upd->execute([$payment_id, $signature, 'paid', $order_id]);
    $upd2 = pdo()->prepare('UPDATE fees SET status=?, paid_on=? WHERE id=?');
    $upd2->execute(['paid', date('Y-m-d H:i:s'), $fee_id]);

    // Notify admins about successful payment
    try {
        $pstmt = pdo()->prepare('SELECT * FROM payments WHERE order_id=?'); $pstmt->execute([$order_id]); $pay = $pstmt->fetch();
        $stu = null;
        if ($pay && $pay['student_id']) {
            $s = pdo()->prepare('SELECT id,name,email FROM students WHERE id=?'); $s->execute([$pay['student_id']]); $stu = $s->fetch();
        }
        $admins = pdo()->query('SELECT name,email FROM admin')->fetchAll();
        $cfg = require __DIR__ . '/../../config.php';
        $subject = 'Razorpay payment received';
        $body = '<p>Payment received via Razorpay.</p>';
        if ($stu) $body .= '<p><strong>Student:</strong> '.e($stu['name']).' (ID: '.e($stu['id']).')</p>';
        $body .= '<p><strong>Order ID:</strong> '.e($order_id).' <br><strong>Payment ID:</strong> '.e($payment_id).'</p>';
        foreach ($admins as $a) { if (!empty($a['email'])) @send_mail($a['email'], $subject, $body, $cfg['mail']['mail_from'] ?? null); }
    } catch (Exception $e) { error_log('Razorpay notify error: '.$e->getMessage()); }

    echo json_encode(['ok'=>true]);
} catch (Exception $e) {
    echo json_encode(['error'=>'verify_failed','message'=>$e->getMessage()]);
}
