<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  if (isset($_POST['action']) && $_POST['action']==='delete'){
    $id = intval($_POST['id'] ?? 0);
    pdo()->prepare('DELETE FROM gallery WHERE id=?')->execute([$id]);
    $success='Deleted';
  } else {
    $caption = trim($_POST['caption'] ?? ''); $category = trim($_POST['category'] ?? '');
    list($ok,$res) = validate_and_move_upload($_FILES['image'] ?? null, 'gallery', ['jpg','jpeg','png']);
    if (!$ok) $error=$res; else { pdo()->prepare('INSERT INTO gallery (image_path,caption,category) VALUES (?,?,?)')->execute([$res,$caption,$category]); $success='Uploaded'; }
  }
}
$items = pdo()->query('SELECT * FROM gallery ORDER BY uploaded_at DESC')->fetchAll();
?>
<h2>Gallery Management</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Image<input type="file" name="image" required></label>
  <label>Caption<input name="caption"></label>
  <label>Category<input name="category"></label>
  <button type="submit">Upload</button>
</form>
<h3>Existing</h3>
<?php foreach($items as $it): ?>
  <div class="card"><img src="<?php echo e($it['image_path']); ?>" alt="<?php echo e($it['caption']); ?>" style="max-width:200px;display:block;margin-bottom:.5rem;">
    <div><?php echo e($it['caption']); ?> — <?php echo e($it['category']); ?></div>
    <form method="post" style="display:inline"><input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $it['id']; ?>"><button>Delete</button></form>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
