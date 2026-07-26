<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
$stu = current_student(); $subject = $stu['subject'];
$stmt = pdo()->prepare('SELECT * FROM quizzes WHERE subject_id=?'); $stmt->execute([$subject]); $quizzes = $stmt->fetchAll();
$resultsStmt = pdo()->prepare('SELECT * FROM quiz_results WHERE student_id=? AND quiz_id=?');
?>
<h2>Quizzes</h2>
<?php if(!$quizzes) echo '<div class="card">No quizzes set for your subject.</div>'; ?>
<?php foreach($quizzes as $q): ?>
  <div class="card">
    <h4><?php echo e($q['title']); ?></h4>
    <a href="/blog">Resources</a>
    <a href="/student/quiz_attempt?id=<?php echo $q['id']; ?>">Attempt Quiz</a>
    <?php $resultsStmt->execute([$stu['id'],$q['id']]); $res = $resultsStmt->fetchAll(); if($res): ?>
      <div>Past Attempts: <?php echo count($res); ?></div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
