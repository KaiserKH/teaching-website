<?php require __DIR__ . '/../../../includes/header.php';
if (!is_student_logged_in()){ header('Location:/login'); exit; }
if (!enforce_session_timeout()) { student_logout(); header('Location:/login'); exit; }
$student = current_student();
?>
<h2>Welcome, <?php echo e($student['name']); ?></h2>
<div class="cards">
  <div class="card">Attendance: <!-- compute --> 92%</div>
  <div class="card">Pending Homework: 2</div>
  <div class="card">Upcoming Quiz: None</div>
</div>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
