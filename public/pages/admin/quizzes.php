<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
// create quiz
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='create_quiz'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $title = trim($_POST['title'] ?? ''); $subject_id = intval($_POST['subject_id'] ?? 0);
  if (empty($title)) $error='Title required';
  if (empty($error)){
    pdo()->prepare('INSERT INTO quizzes (subject_id,title) VALUES (?,?)')->execute([$subject_id,$title]);
    $quiz_id = pdo()->lastInsertId();
    header('Location:/admin/quizzes?quiz_id='.$quiz_id); exit;
  }
}
// add question
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='add_question'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $quiz_id = intval($_POST['quiz_id'] ?? 0); $question = trim($_POST['question'] ?? '');
  $options = array_filter([$_POST['opt1'] ?? '', $_POST['opt2'] ?? '', $_POST['opt3'] ?? '', $_POST['opt4'] ?? '']);
  $correct = $_POST['correct'] ?? '';
  if (empty($question) || empty($options) || empty($correct)) $error='Question, options and correct option required';
  if (empty($error)){
    pdo()->prepare('INSERT INTO quiz_questions (quiz_id,question,options,correct_option) VALUES (?,?,?,?)')
      ->execute([$quiz_id,$question,json_encode($options),$correct]);
    $success='Question added';
  }
}
$subjects = pdo()->query('SELECT id,name FROM subjects')->fetchAll();
$quiz_id = intval($_GET['quiz_id'] ?? 0);
if ($quiz_id) $quiz = pdo()->prepare('SELECT * FROM quizzes WHERE id=?')->execute([$quiz_id]) ? pdo()->prepare('SELECT * FROM quizzes WHERE id=?')->execute([$quiz_id]) : null;
?>
<h2>Manage Quizzes</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="action" value="create_quiz">
  <label>Title<input name="title" required></label>
  <label>Subject<select name="subject_id"><?php foreach($subjects as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo e($s['name']); ?></option><?php endforeach; ?></select></label>
  <button type="submit">Create Quiz</button>
</form>

<?php if($quiz_id):
  $questions = pdo()->prepare('SELECT * FROM quiz_questions WHERE quiz_id=?'); $questions->execute([$quiz_id]); $questions = $questions->fetchAll();
?>
  <h3>Add Question to Quiz #<?php echo $quiz_id; ?></h3>
  <form method="post">
    <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="action" value="add_question">
    <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
    <label>Question<textarea name="question" required></textarea></label>
    <label>Option 1<input name="opt1"></label>
    <label>Option 2<input name="opt2"></label>
    <label>Option 3<input name="opt3"></label>
    <label>Option 4<input name="opt4"></label>
    <label>Correct Option (enter exact option text)<input name="correct"></label>
    <button type="submit">Add Question</button>
  </form>
  <h4>Existing Questions</h4>
  <?php foreach($questions as $q): ?>
    <div class="card"><strong><?php echo e($q['question']); ?></strong></div>
  <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/../../../includes/footer.php'; ?>
