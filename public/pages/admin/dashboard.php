<?php require __DIR__ . '/../../../includes/header.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
$admin = current_admin();
?>
<h2>Admin Dashboard</h2>
<div class="cards">
  <div class="card">Total Students: <?php echo pdo()->query('SELECT COUNT(*) as c FROM students')->fetch()['c']; ?></div>
  <div class="card">Pending Admissions: <?php echo pdo()->query("SELECT COUNT(*) as c FROM admissions WHERE status='pending'")->fetch()['c']; ?></div>
  <div class="card">Unpaid Fees: <?php echo pdo()->query("SELECT COUNT(*) as c FROM fees WHERE status='unpaid'")->fetch()['c']; ?></div>
</div>
<p><a href="/admin/admissions">Manage Admissions</a></p>
<?php require __DIR__ . '/../../../includes/footer.php'; ?>
