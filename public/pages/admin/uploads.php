<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $type = $_POST['type'] ?? 'note';
  if ($type==='note'){
    $title = trim($_POST['title'] ?? '');
    list($ok,$res) = validate_and_move_upload($_FILES['file'] ?? null, 'notes', ['pdf']);
    if (!$ok) $error = $res; else { pdo()->prepare('INSERT INTO notes (subject_id,title,file_path) VALUES (?,?,?)')->execute([intval($_POST['subject_id'] ?? 0), $title, $res]); $success='Note uploaded'; }
  } else {
    $title = trim($_POST['title'] ?? '');
    list($ok,$res) = validate_and_move_upload($_FILES['file'] ?? null, 'videos', ['mp4']);
    if (!$ok) $error = $res; else { pdo()->prepare('INSERT INTO videos (subject_id,title,file_path) VALUES (?,?,?)')->execute([intval($_POST['subject_id'] ?? 0), $title, $res]); $success='Video uploaded'; }
  }
}
$subjects = pdo()->query('SELECT id,name FROM subjects')->fetchAll();
?>
<h2>Upload Notes / Videos</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Type<select name="type"><option value="note">Note (PDF)</option><option value="video">Video (MP4)</option></select></label>
  <label>Subject<select name="subject_id"><?php foreach($subjects as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo e($s['name']); ?></option><?php endforeach; ?></select></label>
  <label>Title<input name="title" required></label>
  <label>File<input type="file" name="file" required></label>
  <button type="submit">Upload</button>
</form>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
