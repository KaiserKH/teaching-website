<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $id = intval($_POST['id'] ?? 0); $action = $_POST['action'] ?? '';
  if ($action==='approve') pdo()->prepare('UPDATE testimonials SET approved=1 WHERE id=?')->execute([$id]);
  if ($action==='reject') pdo()->prepare('UPDATE testimonials SET approved=0 WHERE id=?')->execute([$id]);
  if ($action==='delete') pdo()->prepare('DELETE FROM testimonials WHERE id=?')->execute([$id]);
}
$rows = pdo()->query('SELECT * FROM testimonials ORDER BY created_at DESC')->fetchAll();
?>
<h2>Testimonials</h2>
<?php foreach($rows as $r): ?>
  <div class="card">
    <div><strong><?php echo e($r['name']); ?> (<?php echo e($r['role']); ?>)</strong> — <?php echo e($r['approved']? 'Approved':'Pending'); ?></div>
    <div><?php echo e($r['message']); ?></div>
    <form method="post" style="display:inline">
      <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
      <button name="action" value="approve">Approve</button>
      <button name="action" value="reject">Reject</button>
      <button name="action" value="delete">Delete</button>
    </form>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
