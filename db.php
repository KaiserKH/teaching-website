<?php
$cfg = require __DIR__ . '/config.php';
$db = $cfg['db'];
try {
    $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // In production, hide details
    die('Database connection failed: ' . $e->getMessage());
}

function pdo() {
    global $pdo;
    return $pdo;
}
