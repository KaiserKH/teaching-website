<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $id = intval($_POST['id'] ?? 0);
  $reply = trim($_POST['reply'] ?? '');
  $status = $_POST['status'] ?? 'answered';
  if ($id && $reply !== ''){
    pdo()->prepare('UPDATE doubts SET reply=?, status=? WHERE id=?')->execute([$reply,$status,$id]);
    $row = pdo()->prepare('SELECT d.*, s.email FROM doubts d LEFT JOIN students s ON d.student_id=s.id WHERE d.id=?'); $row->execute([$id]); $r = $row->fetch();
    if ($r && !empty($r['email'])){
      @mail($r['email'], 'Reply to your doubt', "Reply: " . $reply);
    }
    $success='Replied';
  }
}
$rows = pdo()->query('SELECT d.*, st.name as student_name, sub.name as subject_name FROM doubts d LEFT JOIN students st ON d.student_id=st.id LEFT JOIN subjects sub ON d.subject_id=sub.id ORDER BY d.created_at DESC')->fetchAll();
?>
<h2>Student Doubts</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<?php foreach($rows as $r): ?>
  <div class="card">
    <div><strong><?php echo e($r['student_name']); ?></strong> — <?php echo e($r['subject_name'] ?: 'General'); ?> — <?php echo e($r['status']); ?></div>
    <div><?php echo e($r['message']); ?></div>
    <?php if($r['reply']): ?><div style="margin-top:.5rem;background:#f3f4f6;padding:.5rem;border-radius:6px;">Reply: <?php echo e($r['reply']); ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
      <label>Reply<textarea name="reply" required></textarea></label>
      <label>Status<select name="status"><option value="answered">answered</option><option value="open">open</option><option value="closed">closed</option></select></label>
      <button type="submit">Send Reply</button>
    </form>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
