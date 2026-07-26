<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $action = $_POST['action'] ?? '';
  if ($action==='create'){
    $student_id = intval($_POST['student_id'] ?? 0); $amount = floatval($_POST['amount'] ?? 0); $month = $_POST['month'] ?? null; 
    pdo()->prepare('INSERT INTO fees (student_id,amount,month,status) VALUES (?,?,?,?)')->execute([$student_id,$amount,$month,'unpaid']);
    $success='Fee record created';
  } elseif ($action==='mark_paid'){
    $id = intval($_POST['id'] ?? 0); pdo()->prepare('UPDATE fees SET status=?,paid_on=? WHERE id=?')->execute(['paid',date('Y-m-d H:i:s'),$id]); $success='Marked paid';
  } elseif ($action==='mark_unpaid'){
    $id = intval($_POST['id'] ?? 0); pdo()->prepare('UPDATE fees SET status=?,paid_on=NULL WHERE id=?')->execute(['unpaid',$id]); $success='Marked unpaid';
  }
}
$students = pdo()->query('SELECT id,name FROM students ORDER BY name')->fetchAll();
$fees = pdo()->query('SELECT f.*, s.name as student_name FROM fees f LEFT JOIN students s ON f.student_id=s.id ORDER BY f.created_at DESC')->fetchAll();
?>
<h2>Fee Management</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="action" value="create">
  <label>Student<select name="student_id"><?php foreach($students as $st): ?><option value="<?php echo $st['id']; ?>"><?php echo e($st['name']); ?></option><?php endforeach; ?></select></label>
  <label>Amount<input name="amount" type="number" step="0.01" required></label>
  <label>Month<input name="month" type="month"></label>
  <button type="submit">Create Fee Record</button>
</form>
<h3>Records</h3>
<?php foreach($fees as $f): ?>
  <div class="card">
    <div><?php echo e($f['student_name']); ?> — ₹<?php echo e($f['amount']); ?> — <?php echo e($f['month']); ?> — <?php echo e($f['status']); ?></div>
    <form method="post" style="display:inline">
      <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
      <?php if($f['status']==='unpaid'): ?><button name="action" value="mark_paid">Mark Paid</button><?php else: ?><button name="action" value="mark_unpaid">Mark Unpaid</button><?php endif; ?>
    </form>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
