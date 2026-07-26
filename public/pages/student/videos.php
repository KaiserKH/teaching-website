<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
if (!enforce_session_timeout()) { student_logout(); header('Location:/login'); exit; }
$stu = current_student(); $subject = $stu['subject'];
$stmt = pdo()->prepare('SELECT * FROM videos WHERE subject_id=? ORDER BY uploaded_at DESC'); $stmt->execute([$subject]); $videos = $stmt->fetchAll();
?>
<h2>Video Lectures</h2>
<?php if(!$videos) echo '<div class="card">No videos available.</div>'; ?>
<?php foreach($videos as $v): ?>
  <div class="card">
    <h4><?php echo e($v['title']); ?></h4>
    <?php if(!empty($v['video_url'])): ?>
      <div class="video-wrap"><iframe src="<?php echo e($v['video_url']); ?>" width="100%" height="360" frameborder="0" allowfullscreen loading="lazy"></iframe></div>
    <?php elseif(!empty($v['file_path'])): ?>
      <video controls width="100%" preload="metadata">
        <source src="<?php echo e($v['file_path']); ?>" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
