<?php
require __DIR__ . '/../../../functions.php';
if (!is_admin_logged_in()){ header('Location:/admin/login'); exit; }
if (!enforce_session_timeout()) { header('Location:/admin/login'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['_csrf'] ?? '')) { die('Invalid CSRF'); }
  $id = intval($_POST['id'] ?? 0);
  $action = $_POST['action'] ?? '';
  $stmt = pdo()->prepare('SELECT * FROM admissions WHERE id=? LIMIT 1'); $stmt->execute([$id]); $row = $stmt->fetch();
  if (!$row) { header('Location:/admin/admissions'); exit; }
  if ($action==='approve'){
    // create student account
    $password = bin2hex(random_bytes(4));
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = pdo()->prepare('INSERT INTO students (name,parent_name,phone,email,class,subject,address,password_hash,status) VALUES (?,?,?,?,?,?,?,?,?)');
    $ins->execute([$row['name'],$row['parent_name'],$row['phone'],$row['email'],$row['class'],$row['subject_id'],$row['address'],$hash,'approved']);
    pdo()->prepare('UPDATE admissions SET status=? WHERE id=?')->execute(['approved',$id]);
    // send email with credentials (basic)
    @mail($row['email'], 'Admission Approved', "Your account created. Email: {$row['email']} Password: {$password}");
  } else {
    pdo()->prepare('UPDATE admissions SET status=? WHERE id=?')->execute(['rejected',$id]);
  }
}
header('Location:/admin/admissions'); exit;
