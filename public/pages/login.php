<?php require __DIR__ . '/../../includes/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $email = trim($_POST['email'] ?? ''); $pass = $_POST['password'] ?? '';
  $stmt = pdo()->prepare('SELECT id,password_hash,status FROM students WHERE email=? LIMIT 1');
  $stmt->execute([$email]); $u = $stmt->fetch();
  if ($u && $u['status']==='approved' && password_verify($pass,$u['password_hash'])){ student_login($u['id']); header('Location:/student/dashboard'); exit; }
  $error = 'Invalid credentials or not approved yet.';
}
?>
<h2>Student Login</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee;">'.e($error).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Email<input name="email" type="email" required></label>
  <label>Password<input name="password" type="password" required></label>
  <button type="submit">Login</button>
</form>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
