<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';
header('Content-Type: application/json');
if (!is_student_logged_in()) { echo json_encode(['error'=>'unauthenticated']); exit; }

$fee_id = intval($_POST['fee_id'] ?? 0);
$txn_id = trim($_POST['txn_id'] ?? '');
if (!$fee_id || !$txn_id) { echo json_encode(['error'=>'missing']); exit; }

list($ok, $res) = validate_and_move_upload($_FILES['screenshot'] ?? null, 'payments', ['jpg','jpeg','png','pdf']);
if (!$ok) { echo json_encode(['error'=>'upload_failed','message'=>$res]); exit; }

$cfg = require __DIR__ . '/../../config.php';
$stu = current_student();
$ins = pdo()->prepare('INSERT INTO payments (student_id, fee_id, provider, amount, currency, status, receipt, screenshot, note) VALUES (?,?,?,?,?,?,?,?,?)');
$stmtFee = pdo()->prepare('SELECT amount FROM fees WHERE id=?'); $stmtFee->execute([$fee_id]); $fee = $stmtFee->fetch();
$amount = $fee ? $fee['amount'] : 0;
$ins->execute([$stu['id'], $fee_id, 'upi', $amount, 'INR', 'pending', $txn_id, $res, 'UPI upload, txn: '.$txn_id]);
        
		// Notify all admins by email
		try {
			$admins = pdo()->query('SELECT name,email FROM admin')->fetchAll();
			$cfg = require __DIR__ . '/../../config.php';
			$screenshotUrl = $res; // web path
			$adminUrl = rtrim($cfg['base_url'], '/') . '/admin/payments';
			$subject = 'New UPI payment uploaded by ' . ($stu['name'] ?? 'Student');
			$body = '<p>A student has uploaded a UPI payment for review.</p>';
			$body .= '<p><strong>Student:</strong> ' . e($stu['name']) . ' (ID: ' . e($stu['id']) . ')</p>';
			$body .= '<p><strong>Amount:</strong> ₹' . e($amount) . ' — <strong>Txn ID:</strong> ' . e($txn_id) . '</p>';
			$body .= '<p><a href="' . $adminUrl . '">Review payments in admin panel</a></p>';
			$body .= '<p>Screenshot: <a href="' . $screenshotUrl . '" target="_blank">View</a></p>';
			foreach ($admins as $a) {
				if (!empty($a['email'])) {
					@send_mail($a['email'], $subject, $body, $cfg['mail']['mail_from'] ?? null);
				}
			}
		} catch (Exception $e) {
			error_log('Payment notify error: '.$e->getMessage());
		}
        
		echo json_encode(['ok'=>true]);
