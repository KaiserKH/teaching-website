<?php require __DIR__ . '/../../../functions.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
$stu = current_student();
$qid = intval($_GET['id'] ?? 0);
$stmt = pdo()->prepare('SELECT * FROM quizzes WHERE id=? LIMIT 1'); $stmt->execute([$qid]); $quiz = $stmt->fetch();
if (!$quiz) { header('Location:/student/quizzes'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) die('Invalid CSRF');
  $answers = $_POST['q'] ?? [];
  $qstmt = pdo()->prepare('SELECT * FROM quiz_questions WHERE quiz_id=?'); $qstmt->execute([$qid]); $questions = $qstmt->fetchAll();
  $correct = 0; $total = count($questions);
  foreach($questions as $ques){
    $id = $ques['id'];
    $given = $answers[$id] ?? '';
    if ($given === $ques['correct_option']) $correct++;
  }
  $score = $total?round(($correct/$total)*100,2):0;
  $ins = pdo()->prepare('INSERT INTO quiz_results (student_id,quiz_id,score) VALUES (?,?,?)');
  $ins->execute([$stu['id'],$qid,$score]);
  header('Location:/student/results?quiz_id='.$qid); exit;
}
$qstmt = pdo()->prepare('SELECT * FROM quiz_questions WHERE quiz_id=?'); $qstmt->execute([$qid]); $questions = $qstmt->fetchAll();
?>
<?php require __DIR__ . '/../../../includes/header.php'; ?>
<h2>Attempt: <?php echo e($quiz['title']); ?></h2>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <?php foreach($questions as $i=>$ques):
    $opts = json_decode($ques['options'], true);
  ?>
    <div class="card">
      <p><strong>Q<?php echo $i+1; ?>.</strong> <?php echo e($ques['question']); ?></p>
      <?php foreach($opts as $optKey=>$optText): ?>
        <label><input type="radio" name="q[<?php echo $ques['id']; ?>]" value="<?php echo e($optKey); ?>"> <?php echo e($optText); ?></label>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
  <button type="submit">Submit Quiz</button>
</form>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
