<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
if (!enforce_session_timeout()) { student_logout(); header('Location:/login'); exit; }
$stu = current_student(); $subject = $stu['subject'];
$stmt = pdo()->prepare('SELECT * FROM notes WHERE subject_id=? ORDER BY uploaded_at DESC'); $stmt->execute([$subject]); $notes = $stmt->fetchAll();
?>
<h2>Notes & PDFs</h2>
<?php if(!$notes) echo '<div class="card">No notes uploaded yet.</div>'; ?>
<?php foreach($notes as $n): ?>
  <div class="card">
    <h4><?php echo e($n['title']); ?></h4>
    <div>Uploaded: <?php echo e($n['uploaded_at']); ?></div>
    <a href="<?php echo e($n['file_path']); ?>" download>Download</a>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
