<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
$stu = current_student();
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $subject_id = intval($_POST['subject_id'] ?? 0);
  $message = trim($_POST['message'] ?? '');
  if (empty($message)) $error='Please write your doubt';
  if (empty($error)){
    $stmt = pdo()->prepare('INSERT INTO doubts (student_id,subject_id,message) VALUES (?,?,?)');
    $stmt->execute([$stu['id'],$subject_id,$message]);
    $success='Doubt submitted';
  }
}
$rows = pdo()->prepare('SELECT d.*, s.name as subject_name FROM doubts d LEFT JOIN subjects s ON d.subject_id=s.id WHERE d.student_id=? ORDER BY created_at DESC');
$rows->execute([$stu['id']]); $rows = $rows->fetchAll();
$subjects = pdo()->query('SELECT id,name FROM subjects')->fetchAll();
?>
<h2>Doubts</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Subject<select name="subject_id">
    <?php foreach($subjects as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo e($s['name']); ?></option><?php endforeach; ?>
  </select></label>
  <label>Message<textarea name="message" required></textarea></label>
  <button type="submit">Submit Doubt</button>
</form>
<h3>Your Doubts</h3>
<?php foreach($rows as $r): ?>
  <div class="card">
    <div><strong><?php echo e($r['subject_name'] ?: 'General'); ?></strong> — <?php echo e($r['status']); ?></div>
    <div><?php echo e($r['message']); ?></div>
    <?php if(!empty($r['reply'])): ?><div style="margin-top:.5rem;background:#f3f4f6;padding:.5rem;border-radius:6px;"><strong>Reply:</strong> <?php echo e($r['reply']); ?></div><?php endif; ?>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
