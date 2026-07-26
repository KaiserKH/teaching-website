<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
if (!enforce_session_timeout()) { student_logout(); header('Location:/login'); exit; }
$stu = current_student();
$quiz_id = intval($_GET['quiz_id'] ?? 0);
if ($quiz_id){
  $stmt = pdo()->prepare('SELECT qr.*, q.title FROM quiz_results qr JOIN quizzes q ON qr.quiz_id=q.id WHERE qr.student_id=? AND qr.quiz_id=? ORDER BY attempted_at DESC');
  $stmt->execute([$stu['id'],$quiz_id]);
  $rows = $stmt->fetchAll();
} else {
  $stmt = pdo()->prepare('SELECT qr.*, q.title FROM quiz_results qr JOIN quizzes q ON qr.quiz_id=q.id WHERE qr.student_id=? ORDER BY attempted_at DESC');
  $stmt->execute([$stu['id']]); $rows = $stmt->fetchAll();
}
?>
<h2>Quiz Results</h2>
<?php if(!$rows) echo '<div class="card">No quiz attempts yet.</div>'; ?>
<?php foreach($rows as $r): ?>
  <div class="card">
    <div><strong><?php echo e($r['title']); ?></strong> — <?php echo e($r['score']); ?>%</div>
    <div>Attempted at: <?php echo e($r['attempted_at']); ?></div>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
