<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $action = $_POST['action'] ?? '';
  if ($action==='delete'){
    $id = intval($_POST['id'] ?? 0); pdo()->prepare('DELETE FROM blog_posts WHERE id=?')->execute([$id]); $success='Deleted';
  } else {
    $title = trim($_POST['title'] ?? ''); $content = $_POST['content'] ?? '';
    $slug = strtolower(preg_replace('/[^a-z0-9]+/','-',trim($title)));
    pdo()->prepare('INSERT INTO blog_posts (title,slug,content,published_at) VALUES (?,?,?,?)')->execute([$title,$slug,$content,date('Y-m-d H:i:s')]);
    $success='Post created';
  }
}
$posts = pdo()->query('SELECT * FROM blog_posts ORDER BY created_at DESC')->fetchAll();
?>
<h2>Blog Management</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Title<input name="title" required></label>
  <label>Content<textarea name="content" required></textarea></label>
  <button type="submit">Publish</button>
</form>
<h3>Existing Posts</h3>
<?php foreach($posts as $p): ?>
  <div class="card"><strong><?php echo e($p['title']); ?></strong> — <a href="/blog/post?slug=<?php echo urlencode($p['slug']); ?>" target="_blank">View</a>
    <form method="post" style="display:inline"><input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $p['id']; ?>"><button>Delete</button></form>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
