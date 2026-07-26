<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
$admin = current_admin();
?>
<h2>Admin Dashboard</h2>
<div class="cards">
  <div class="card">Total Students: <?php echo pdo()->query('SELECT COUNT(*) as c FROM students')->fetch()['c']; ?></div>
  <div class="card">Pending Admissions: <?php echo pdo()->query("SELECT COUNT(*) as c FROM admissions WHERE status='pending'")->fetch()['c']; ?></div>
  <div class="card">Unpaid Fees: <?php echo pdo()->query("SELECT COUNT(*) as c FROM fees WHERE status='unpaid'")->fetch()['c']; ?></div>
</div>
<p><a href="/admin/admissions">Manage Admissions</a> | <a href="/admin/students">Students</a> | <a href="/admin/fees">Fees</a> | <a href="/admin/courses">Courses</a> | <a href="/admin/schedule">Schedule</a> | <a href="/admin/uploads">Uploads</a> | <a href="/admin/quizzes">Quizzes</a> | <a href="/admin/results">Results</a> | <a href="/admin/gallery">Gallery</a> | <a href="/admin/blog">Blog</a> | <a href="/admin/testimonials">Testimonials</a> | <a href="/admin/announcements">Announcements</a> | <a href="/admin/doubts">Doubts</a></p>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
