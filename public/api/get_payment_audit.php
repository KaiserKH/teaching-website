<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';
header('Content-Type: application/json');
if (!is_admin_logged_in()) { echo json_encode(['error'=>'unauthenticated']); exit; }
$payment_id = intval($_GET['payment_id'] ?? 0);
if (!$payment_id) { echo json_encode(['error'=>'missing_payment_id']); exit; }

$pstmt = pdo()->prepare('SELECT p.*, s.name as student_name FROM payments p LEFT JOIN students s ON p.student_id=s.id WHERE p.id=?'); $pstmt->execute([$payment_id]); $payment = $pstmt->fetch();
if (!$payment) { echo json_encode(['error'=>'not_found']); exit; }

$auditStmt = pdo()->prepare('SELECT a.*, ad.name as admin_name FROM payment_audit a LEFT JOIN admin ad ON a.admin_id=ad.id WHERE a.payment_id=? ORDER BY a.created_at DESC');
$auditStmt->execute([$payment_id]); $audits = $auditStmt->fetchAll();

echo json_encode(['payment'=>$payment, 'audits'=>$audits]);
