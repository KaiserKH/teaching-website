<?php require __DIR__ . '/../../includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) { $error='Invalid CSRF token'; }
  $name = trim($_POST['name'] ?? '');
  $parent = trim($_POST['parent_name'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $class = trim($_POST['class'] ?? '');
  $subject = intval($_POST['subject'] ?? 0);
  $address = trim($_POST['address'] ?? '');
  if (empty($name) || empty($phone) || empty($class)) $error='Please fill required fields';
  if (empty($error)){
    $stmt = pdo()->prepare('INSERT INTO admissions (name,parent_name,phone,email,class,subject_id,address) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$name,$parent,$phone,$email,$class,$subject,$address]);
    $success = 'Admission request submitted. You will be contacted.';
  }
}
$subs = pdo()->query('SELECT id,name FROM subjects')->fetchAll();
?>
<h2>Admission Form</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee;">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe;">'.e($success).'</div>'; ?>
<form method="post" novalidate>
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Student Name<input name="name" required></label>
  <label>Parent Name<input name="parent_name"></label>
  <label>Phone<input name="phone" required></label>
  <label>Email<input name="email" type="email"></label>
  <label>Class<input name="class" required></label>
  <label>Subject<select name="subject">
    <option value="0">--select--</option>
    <?php foreach($subs as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo e($s['name']); ?></option><?php endforeach; ?>
  </select></label>
  <label>Address<textarea name="address"></textarea></label>
  <button type="submit">Submit</button>
</form>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
