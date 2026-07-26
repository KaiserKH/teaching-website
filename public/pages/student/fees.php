<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
$stu = current_student();
$stmt = pdo()->prepare('SELECT * FROM fees WHERE student_id=? ORDER BY created_at DESC'); $stmt->execute([$stu['id']]); $fees = $stmt->fetchAll();
?>
<h2>Fees</h2>
<?php if(!$fees) echo '<div class="card">No fee records found.</div>'; ?>
<?php foreach($fees as $f): ?>
  <div class="card">
    <div>Amount: ₹<?php echo e($f['amount']); ?> — Status: <?php echo e($f['status']); ?></div>
    <div>Month: <?php echo e($f['month']); ?> | Paid on: <?php echo e($f['paid_on']); ?></div>
    <?php if($f['status']==='paid'): ?><a href="#">Download Receipt</a><?php endif; ?>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
