<?php
session_start();
require_once __DIR__ . '/db.php';

// CSRF helpers
function csrf_token() {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function verify_csrf($token) {
    return hash_equals($_SESSION['_csrf_token'] ?? '', $token ?? '');
}

// Simple escape
function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

// Auth helpers for admin
function admin_login($adminId) {
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $adminId;
}

function admin_logout() {
    unset($_SESSION['admin_id']);
}

function is_admin_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function current_admin() {
    if (!is_admin_logged_in()) return null;
    $stmt = pdo()->prepare('SELECT id,name,email FROM admin WHERE id=?');
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

// Student auth
function student_login($studentId) {
    session_regenerate_id(true);
    $_SESSION['student_id'] = $studentId;
}
function student_logout() { unset($_SESSION['student_id']); }
function is_student_logged_in() { return !empty($_SESSION['student_id']); }
function current_student() {
    if (!is_student_logged_in()) return null;
    $stmt = pdo()->prepare('SELECT id,name,email,class,subject FROM students WHERE id=?');
    $stmt->execute([$_SESSION['student_id']]);
    return $stmt->fetch();
}

// Simplified file upload validator
function validate_and_move_upload($file, $targetDir, $allowed = ['pdf','jpg','jpeg','png','mp4'], $maxSize = null) {
    $maxSize = $maxSize ?? (10 * 1024 * 1024);
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) return [false, 'File upload error'];
    if ($file['size'] > $maxSize) return [false, 'File too large'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return [false, 'Invalid file type'];
    $fname = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    $dest = rtrim($targetDir, '/') . '/' . $fname;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return [false, 'Failed to move file'];
    return [true, $dest];
}
