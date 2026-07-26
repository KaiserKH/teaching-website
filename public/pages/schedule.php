<?php require __DIR__ . '/../../includes/header.php';
$stmt = pdo()->query('SELECT s.*, sub.name as subject FROM schedule s LEFT JOIN subjects sub ON s.subject_id=sub.id ORDER BY s.day_of_week, s.start_time');
$rows = $stmt->fetchAll();
?>
<h2>Weekly Schedule</h2>
<table class="card" style="width:100%;border-collapse:collapse">
  <thead><tr><th>Day</th><th>Time</th><th>Subject</th><th>Batch</th></tr></thead>
  <tbody>
  <?php foreach($rows as $r): ?>
    <tr><td><?php echo e($r['day_of_week']); ?></td><td><?php echo e(substr($r['start_time'],0,5)); ?> - <?php echo e(substr($r['end_time'],0,5)); ?></td><td><?php echo e($r['subject']); ?></td><td><?php echo e($r['batch_name']); ?></td></tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
