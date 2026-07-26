<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
// Add course
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '') === 'add'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $name = trim($_POST['name'] ?? ''); $class_level = trim($_POST['class_level'] ?? ''); $fees = floatval($_POST['fees'] ?? 0); $duration = trim($_POST['duration'] ?? ''); $mode = $_POST['mode'] ?? 'offline';
  if (empty($name)) $error='Name required';
  if (empty($error)){
    $stmt = pdo()->prepare('INSERT INTO subjects (name,class_level,fees,duration,mode) VALUES (?,?,?,?,?)');
    $stmt->execute([$name,$class_level,$fees,$duration,$mode]);
    $success='Subject added';
  }
}
// Delete
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '') === 'delete'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $id = intval($_POST['id'] ?? 0);
  pdo()->prepare('DELETE FROM subjects WHERE id=?')->execute([$id]);
  $success='Deleted';
}
$subjects = pdo()->query('SELECT * FROM subjects ORDER BY created_at DESC')->fetchAll();
?>
<h2>Manage Courses / Subjects</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="action" value="add">
  <label>Name<input name="name" required></label>
  <label>Class Level<input name="class_level"></label>
  <label>Fees<input name="fees" type="number" step="0.01"></label>
  <label>Duration<input name="duration"></label>
  <label>Mode<select name="mode"><option>offline</option><option>online</option><option>hybrid</option></select></label>
  <button type="submit">Add Subject</button>
</form>
<h3>Existing</h3>
<?php foreach($subjects as $s): ?>
  <div class="card">
    <strong><?php echo e($s['name']); ?></strong> — <?php echo e($s['class_level']); ?> | ₹<?php echo e($s['fees']); ?> | <?php echo e($s['mode']); ?>
    <form method="post" style="display:inline">
      <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
      <button type="submit">Delete</button>
    </form>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
