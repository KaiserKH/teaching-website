<?php require __DIR__ . '/../../includes/header.php';
$stmt = pdo()->query('SELECT * FROM subjects');
$subjects = $stmt->fetchAll();
?>
<h2>Courses / Subjects</h2>
<div class="cards">
  <?php foreach($subjects as $s): ?>
    <div class="card">
      <h4><?php echo e($s['name']); ?></h4>
      <div>Class: <?php echo e($s['class_level']); ?></div>
      <div>Fees: ₹<?php echo e($s['fees']); ?></div>
      <div>Mode: <?php echo e($s['mode']); ?></div>
    </div>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
