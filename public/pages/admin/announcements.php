<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $title = trim($_POST['title'] ?? ''); $message = trim($_POST['message'] ?? ''); $target = $_POST['target'] ?? 'all'; $target_value = trim($_POST['target_value'] ?? '');
  if (empty($title) || empty($message)) $error='Title and message required';
  if (empty($error)){
    pdo()->prepare('INSERT INTO announcements (title,message,target,target_value) VALUES (?,?,?,?)')->execute([$title,$message,$target,$target_value]);
    $success='Announcement created';
    // TODO: optionally send notifications to students
  }
}
$rows = pdo()->query('SELECT * FROM announcements ORDER BY created_at DESC')->fetchAll();
?>
<h2>Announcements</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Title<input name="title" required></label>
  <label>Message<textarea name="message" required></textarea></label>
  <label>Target<select name="target"><option value="all">All</option><option value="class">Class</option><option value="subject">Subject</option></select></label>
  <label>Target Value (class name or subject id)<input name="target_value"></label>
  <button type="submit">Create</button>
</form>
<h3>Existing</h3>
<?php foreach($rows as $r): ?>
  <div class="card"><strong><?php echo e($r['title']); ?></strong> — <?php echo e($r['target']); ?> <?php echo e($r['target_value']); ?>
    <div><?php echo e($r['message']); ?></div>
    <div style="font-size:.9rem;color:#666">Posted: <?php echo e($r['created_at']); ?></div>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
