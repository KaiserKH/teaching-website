<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $student_id = intval($_POST['student_id'] ?? 0);
  $subject_id = intval($_POST['subject_id'] ?? 0);
  $exam_name = trim($_POST['exam_name'] ?? '');
  $marks = floatval($_POST['marks'] ?? 0);
  $max = floatval($_POST['max_marks'] ?? 0);
  $date = $_POST['date'] ?? date('Y-m-d');
  if (empty($exam_name) || !$student_id) $error='Fill required';
  if (empty($error)){
    pdo()->prepare('INSERT INTO results (student_id,subject_id,exam_name,marks,max_marks,date) VALUES (?,?,?,?,?,?)')
      ->execute([$student_id,$subject_id,$exam_name,$marks,$max,$date]);
    $success='Result recorded';
  }
}
$students = pdo()->query('SELECT id,name FROM students ORDER BY name')->fetchAll();
$subjects = pdo()->query('SELECT id,name FROM subjects')->fetchAll();
?>
<h2>Enter Results</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Student<select name="student_id"><?php foreach($students as $st): ?><option value="<?php echo $st['id']; ?>"><?php echo e($st['name']); ?></option><?php endforeach; ?></select></label>
  <label>Subject<select name="subject_id"><?php foreach($subjects as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo e($s['name']); ?></option><?php endforeach; ?></select></label>
  <label>Exam Name<input name="exam_name"></label>
  <label>Marks<input name="marks" type="number" step="0.01"></label>
  <label>Max Marks<input name="max_marks" type="number" step="0.01"></label>
  <label>Date<input name="date" type="date" value="<?php echo date('Y-m-d'); ?>"></label>
  <button type="submit">Save Result</button>
</form>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
