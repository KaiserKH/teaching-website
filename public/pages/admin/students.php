<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
// actions: deactivate/activate/reset_password/edit
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $action = $_POST['action'] ?? '';
  $id = intval($_POST['id'] ?? 0);
  if ($action==='deactivate'){
    pdo()->prepare('UPDATE students SET status=? WHERE id=?')->execute(['rejected',$id]); $success='Deactivated';
  } elseif ($action==='activate'){
    pdo()->prepare('UPDATE students SET status=? WHERE id=?')->execute(['approved',$id]); $success='Activated';
  } elseif ($action==='reset'){
    $new = bin2hex(random_bytes(4)); $hash = password_hash($new, PASSWORD_DEFAULT);
    pdo()->prepare('UPDATE students SET password_hash=? WHERE id=?')->execute([$hash,$id]);
    $em = pdo()->prepare('SELECT email FROM students WHERE id=?'); $em->execute([$id]); $row = $em->fetch(); if ($row && !empty($row['email'])) @mail($row['email'],'Password Reset','Your new password: '.$new);
    $success='Password reset and emailed (if exists)';
  } elseif ($action==='edit'){
    $name = trim($_POST['name'] ?? ''); $parent = trim($_POST['parent_name'] ?? ''); $phone = trim($_POST['phone'] ?? ''); $email = trim($_POST['email'] ?? ''); $class = trim($_POST['class'] ?? '');
    pdo()->prepare('UPDATE students SET name=?,parent_name=?,phone=?,email=?,class=? WHERE id=?')->execute([$name,$parent,$phone,$email,$class,$id]);
    $success='Student updated';
  }
}
$q = pdo()->query('SELECT s.*, sub.name as subject_name FROM students s LEFT JOIN subjects sub ON s.subject=sub.id ORDER BY s.created_at DESC');
$students = $q->fetchAll();
?>
<h2>Manage Students</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<?php foreach($students as $st): ?>
  <div class="card">
    <form method="post">
      <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="id" value="<?php echo $st['id']; ?>">
      <label>Name<input name="name" value="<?php echo e($st['name']); ?>"></label>
      <label>Parent<input name="parent_name" value="<?php echo e($st['parent_name']); ?>"></label>
      <label>Phone<input name="phone" value="<?php echo e($st['phone']); ?>"></label>
      <label>Email<input name="email" value="<?php echo e($st['email']); ?>"></label>
      <label>Class<input name="class" value="<?php echo e($st['class']); ?>"></label>
      <div>Subject: <?php echo e($st['subject_name']); ?></div>
      <button name="action" value="edit">Save</button>
      <?php if($st['status']==='approved'): ?>
        <button name="action" value="deactivate">Deactivate</button>
      <?php else: ?>
        <button name="action" value="activate">Approve</button>
      <?php endif; ?>
      <button name="action" value="reset">Reset Password</button>
    </form>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
