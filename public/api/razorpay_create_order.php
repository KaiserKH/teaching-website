<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

// JSON response
header('Content-Type: application/json');
if (!is_student_logged_in()) { echo json_encode(['error'=>'unauthenticated']); exit; }
$cfg = require __DIR__ . '/../../config.php';
$rz = $cfg['razorpay'];
if (empty($rz['key_id']) || empty($rz['key_secret'])) {
    echo json_encode(['error'=>'razorpay_not_configured']); exit;
}

$fee_id = intval($_POST['fee_id'] ?? 0);
if (!$fee_id) { echo json_encode(['error'=>'missing_fee_id']); exit; }

$stmt = pdo()->prepare('SELECT * FROM fees WHERE id=?'); $stmt->execute([$fee_id]); $fee = $stmt->fetch();
if (!$fee) { echo json_encode(['error'=>'fee_not_found']); exit; }

require_once __DIR__ . '/../../vendor/autoload.php';
try {
    $api = new \Razorpay\Api\Api($rz['key_id'], $rz['key_secret']);
    $amountPaise = intval(round($fee['amount'] * 100));
    $order  = $api->order->create([ 'receipt' => 'fee_'.$fee['id'].'_stu_'.$fee['student_id'], 'amount' => $amountPaise, 'currency' => 'INR', 'payment_capture' => 1 ]);
    // record a created payment
    $ins = pdo()->prepare('INSERT INTO payments (student_id, fee_id, provider, order_id, amount, currency, status, receipt) VALUES (?,?,?,?,?,?,?,?)');
    $ins->execute([$fee['student_id'], $fee['id'], 'razorpay', $order['id'], $fee['amount'], 'INR', 'created', $order['receipt'] ?? '']);
    echo json_encode(['order'=>$order, 'key_id'=>$rz['key_id']]);
} catch (Exception $e) {
    echo json_encode(['error'=>'exception','message'=>$e->getMessage()]);
}
