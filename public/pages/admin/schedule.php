<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) $error='Invalid CSRF';
  $subject_id = intval($_POST['subject_id'] ?? 0);
  $day = intval($_POST['day_of_week'] ?? 1);
  $start = $_POST['start_time'] ?? '00:00';
  $end = $_POST['end_time'] ?? '00:00';
  $batch = trim($_POST['batch_name'] ?? ''); $mode = $_POST['mode'] ?? 'online';
  if (empty($error)){
    pdo()->prepare('INSERT INTO schedule (subject_id,day_of_week,start_time,end_time,batch_name,mode) VALUES (?,?,?,?,?,?)')
      ->execute([$subject_id,$day,$start,$end,$batch,$mode]);
    $success='Schedule added';
  }
}
$subjects = pdo()->query('SELECT id,name FROM subjects')->fetchAll();
$rows = pdo()->query('SELECT sc.*, s.name as subject FROM schedule sc LEFT JOIN subjects s ON sc.subject_id=s.id ORDER BY sc.day_of_week, sc.start_time')->fetchAll();
?>
<h2>Manage Schedule</h2>
<?php if(!empty($error)) echo '<div class="card" style="background:#fee">'.e($error).'</div>'; ?>
<?php if(!empty($success)) echo '<div class="card" style="background:#efe">'.e($success).'</div>'; ?>
<form method="post">
  <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
  <label>Subject<select name="subject_id"><?php foreach($subjects as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo e($s['name']); ?></option><?php endforeach; ?></select></label>
  <label>Day<select name="day_of_week"><option value="1">Mon</option><option value="2">Tue</option><option value="3">Wed</option><option value="4">Thu</option><option value="5">Fri</option><option value="6">Sat</option><option value="0">Sun</option></select></label>
  <label>Start<input name="start_time" type="time"></label>
  <label>End<input name="end_time" type="time"></label>
  <label>Batch<input name="batch_name"></label>
  <label>Mode<select name="mode"><option>online</option><option>offline</option><option>hybrid</option></select></label>
  <button type="submit">Add Slot</button>
</form>
<h3>Weekly</h3>
<?php foreach($rows as $r): ?>
  <div class="card"><?php echo e($r['subject']); ?> — Day: <?php echo e($r['day_of_week']); ?> Time: <?php echo e(substr($r['start_time'],0,5)); ?>-<?php echo e(substr($r['end_time'],0,5)); ?> (<?php echo e($r['mode']); ?>)</div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
