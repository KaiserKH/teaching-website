<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
if (!enforce_session_timeout()) { student_logout(); header('Location:/login'); exit; }
$stu = current_student();
$sid = $stu['id'];
$total = pdo()->prepare('SELECT COUNT(*) as c FROM attendance WHERE student_id=?'); $total->execute([$sid]); $total = $total->fetch()['c'] ?: 0;
$present = pdo()->prepare("SELECT COUNT(*) as c FROM attendance WHERE student_id=? AND status='present'"); $present->execute([$sid]); $present = $present->fetch()['c'] ?: 0;
$percent = $total?round(($present/$total)*100,2):0;
$rows = pdo()->prepare('SELECT date,status FROM attendance WHERE student_id=? ORDER BY date DESC LIMIT 200'); $rows->execute([$sid]); $rows = $rows->fetchAll();
?>
<h2>Attendance</h2>
<div class="cards">
  <div class="card">Total days: <?php echo e($total); ?></div>
  <div class="card">Present: <?php echo e($present); ?></div>
  <div class="card">Attendance %: <?php echo e($percent); ?>%</div>
</div>
<table class="card" style="width:100%;margin-top:1rem">
  <thead><tr><th>Date</th><th>Status</th></tr></thead>
  <tbody>
  <?php foreach($rows as $r): ?>
    <tr><td><?php echo e($r['date']); ?></td><td><?php echo e($r['status']); ?></td></tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
