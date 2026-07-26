<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin_login'); exit; }
if (!enforce_session_timeout()) { admin_logout(); header('Location:/admin_login'); exit; }

$payment_id = intval($_GET['payment_id'] ?? 0);
if (!$payment_id) { echo '<div class="card">Missing payment id.</div>'; require __DIR__ . '/../../../includes/footer.php'; exit; }

$pstmt = pdo()->prepare('SELECT p.*, s.name as student_name FROM payments p LEFT JOIN students s ON p.student_id=s.id WHERE p.id=?'); $pstmt->execute([$payment_id]); $payment = $pstmt->fetch();
if (!$payment) { echo '<div class="card">Payment not found.</div>'; require __DIR__ . '/../../../includes/footer.php'; exit; }

$auditStmt = pdo()->prepare('SELECT a.*, ad.name as admin_name FROM payment_audit a LEFT JOIN admin ad ON a.admin_id=ad.id WHERE a.payment_id=? ORDER BY a.created_at DESC');
$auditStmt->execute([$payment_id]); $audits = $auditStmt->fetchAll();
?>
<h2>Payment History - ID <?php echo intval($payment_id); ?></h2>
<div class="card">
  <div><strong>Student:</strong> <?php echo e($payment['student_name']); ?> (ID: <?php echo e($payment['student_id']); ?>)</div>
  <div><strong>Provider:</strong> <?php echo e($payment['provider']); ?> | <strong>Amount:</strong> ₹<?php echo e($payment['amount']); ?> | <strong>Status:</strong> <?php echo e($payment['status']); ?></div>
  <div><strong>Receipt/Txn:</strong> <?php echo e($payment['receipt']); ?></div>
  <?php if($payment['screenshot']): ?><div><strong>Screenshot:</strong> <a href="<?php echo e($payment['screenshot']); ?>" target="_blank">Open</a></div><?php endif; ?>
</div>

<h3>Audit Log</h3>
<?php if(!$audits) echo '<div class="card">No audit entries.</div>'; ?>
<?php foreach($audits as $a): ?>
  <div class="card">
    <div><strong>Action:</strong> <?php echo e($a['action']); ?> by <?php echo e($a['admin_name'] ?? 'system'); ?></div>
    <div><strong>When:</strong> <?php echo e($a['created_at']); ?></div>
    <?php if($a['note']): ?><div><strong>Note:</strong> <?php echo e($a['note']); ?></div><?php endif; ?>
  </div>
<?php endforeach; ?>

<p><a href="/admin/payments">Back to payments</a></p>

<?php require __DIR__ . '/../../../includes/footer.php'; ?>
