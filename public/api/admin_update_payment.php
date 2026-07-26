<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';
header('Content-Type: application/json');
if (!is_admin_logged_in()) { echo json_encode(['error'=>'unauthenticated']); exit; }
$payment_id = intval($_POST['payment_id'] ?? 0);
$action = $_POST['action'] ?? '';
if (!$payment_id || !$action) { echo json_encode(['error'=>'missing']); exit; }

$pstmt = pdo()->prepare('SELECT * FROM payments WHERE id=?'); $pstmt->execute([$payment_id]); $p = $pstmt->fetch();
if (!$p) { echo json_encode(['error'=>'not_found']); exit; }

if ($action === 'approve') {
    $upd = pdo()->prepare('UPDATE payments SET status=?, payment_id=COALESCE(payment_id, ?), updated_at=NOW() WHERE id=?');
    $upd->execute(['paid', $p['payment_id'] ?? 'manual', $payment_id]);
    if ($p['fee_id']) {
        $u2 = pdo()->prepare('UPDATE fees SET status=?, paid_on=? WHERE id=?');
        $u2->execute(['paid', date('Y-m-d H:i:s'), $p['fee_id']]);
    }
    // audit entry
    try {
        $admin = current_admin();
        $aud = pdo()->prepare('INSERT INTO payment_audit (payment_id, admin_id, action, note) VALUES (?,?,?,?)');
        $aud->execute([$payment_id, $admin['id'] ?? null, 'approve', 'Marked paid by admin']);
        // notify student
        if ($p['student_id']) {
            $s = pdo()->prepare('SELECT id,name,email FROM students WHERE id=?'); $s->execute([$p['student_id']]); $stu = $s->fetch();
            if ($stu && !empty($stu['email'])) {
                $cfg = require __DIR__ . '/../../config.php';
                $sub = 'Payment received';
                $body = '<p>Your payment of ₹'.e($p['amount']).' has been marked paid by the admin.</p>';
                $body .= '<p>Receipt/Txn: '.e($p['receipt'] ?? '').'</p>';
                @send_mail($stu['email'], $sub, $body, $cfg['mail']['mail_from'] ?? null);
            }
        }
    } catch (Exception $e) { error_log('Audit error: '.$e->getMessage()); }
    echo json_encode(['ok'=>true]); exit;
} elseif ($action === 'fail') {
    $upd = pdo()->prepare('UPDATE payments SET status=?, updated_at=NOW() WHERE id=?');
    $upd->execute(['failed', $payment_id]);
    try {
        $admin = current_admin();
        $aud = pdo()->prepare('INSERT INTO payment_audit (payment_id, admin_id, action, note) VALUES (?,?,?,?)');
        $aud->execute([$payment_id, $admin['id'] ?? null, 'fail', 'Marked failed by admin']);
        if ($p['student_id']) {
            $s = pdo()->prepare('SELECT id,name,email FROM students WHERE id=?'); $s->execute([$p['student_id']]); $stu = $s->fetch();
            if ($stu && !empty($stu['email'])) {
                $cfg = require __DIR__ . '/../../config.php';
                $sub = 'Payment review result';
                $body = '<p>Your payment of ₹'.e($p['amount']).' has been marked <strong>failed</strong> by the admin. Please contact support.</p>';
                @send_mail($stu['email'], $sub, $body, $cfg['mail']['mail_from'] ?? null);
            }
        }
    } catch (Exception $e) { error_log('Audit error: '.$e->getMessage()); }
    echo json_encode(['ok'=>true]); exit;
} else {
    echo json_encode(['error'=>'invalid_action']); exit;
}
