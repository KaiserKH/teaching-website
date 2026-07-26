<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
if (!enforce_session_timeout()) { student_logout(); header('Location:/login'); exit; }
$stu = current_student();
$sid = $stu['id'];
$subject = $stu['subject'];
$stmt = pdo()->prepare('SELECT * FROM homework WHERE subject_id=? ORDER BY due_date DESC');
$stmt->execute([$subject]);
$homework = $stmt->fetchAll();
?>
<h2>Homework</h2>
<?php if(!$homework) echo '<div class="card">No homework found.</div>'; ?>
<?php foreach($homework as $h): ?>
  <div class="card">
    <h4><?php echo e($h['title']); ?></h4>
    <div>Due: <?php echo e($h['due_date']); ?></div>
    <p><?php echo e($h['description']); ?></p>
    <?php if(!empty($h['file_path'])): ?><a href="<?php echo e($h['file_path']); ?>" download>Download Attachment</a><?php endif; ?>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
