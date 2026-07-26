<?php require __DIR__ . '/../../includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF token';
  $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $phone = trim($_POST['phone'] ?? ''); $msg = trim($_POST['message'] ?? '');
  if (empty($name) || empty($msg)) $error='Please fill required fields';
  if (empty($error)){
    $stmt = pdo()->prepare('INSERT INTO contact_messages (name,email,phone,message) VALUES (?,?,?,?)');
    $stmt->execute([$name,$email,$phone,$msg]);
    $success = 'Message sent. Thank you.';
    // Optionally: mail admin
  }
}
?>
<h2>Contact</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee;">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe;">'.e($success).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Name<input name="name" required></label>
  <label>Email<input name="email" type="email"></label>
  <label>Phone<input name="phone"></label>
  <label>Message<textarea name="message" required></textarea></label>
  <button type="submit">Send</button>
</form>
<div style="margin-top:1rem">
  <h3>Location</h3>
  <iframe src="https://www.google.com/maps/embed?pb=!1m18" width="100%" height="250" style="border:0;" loading="lazy"></iframe>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
