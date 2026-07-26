<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
$rows = pdo()->query('SELECT a.*, s.name as subject FROM admissions a LEFT JOIN subjects s ON a.subject_id=s.id ORDER BY submitted_at DESC')->fetchAll();
?>
<h2>Admissions</h2>
<?php foreach($rows as $r): ?>
  <div class="card">
    <div><strong><?php echo e($r['name']); ?> (<?php echo e($r['class']); ?>)</strong> - <?php echo e($r['phone']); ?></div>
    <div>Subject: <?php echo e($r['subject']); ?> | Status: <?php echo e($r['status']); ?></div>
    <?php if($r['status']==='pending'): ?>
      <form method="post" action="/admin/approve">
        <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
        <button name="action" value="approve">Approve</button>
        <button name="action" value="reject">Reject</button>
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
