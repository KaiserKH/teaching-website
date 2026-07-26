<?php
// Load environment variables from .env if present
function load_env($path) {
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($k,$v) = explode('=', $line, 2);
        $k = trim($k); $v = trim($v);
        $v = trim($v, "\"'");
        $env[$k] = $v;
    }
    return $env;
}

$env = load_env(__DIR__ . '/.env');

return [
    'db' => [
        'host' => $env['DB_HOST'] ?? '127.0.0.1',
        'port' => intval($env['DB_PORT'] ?? 3306),
        'dbname' => $env['DB_NAME'] ?? 'teaching_website',
        'user' => $env['DB_USER'] ?? 'root',
        'pass' => $env['DB_PASS'] ?? '',
        'charset' => $env['DB_CHARSET'] ?? 'utf8mb4'
    ],
    'base_url' => $env['BASE_URL'] ?? '/',
    'uploads_dir' => __DIR__ . '/' . ($env['UPLOADS_DIR'] ?? 'uploads'),
    'max_upload_size' => intval($env['MAX_UPLOAD_SIZE'] ?? (10 * 1024 * 1024)) // 10MB default
    ,
    // Mail and payment providers
    'mail' => [
        'smtp_host' => $env['SMTP_HOST'] ?? '',
        'smtp_user' => $env['SMTP_USER'] ?? '',
        'smtp_pass' => $env['SMTP_PASS'] ?? '',
        'smtp_port' => intval($env['SMTP_PORT'] ?? 587),
        'mail_from' => $env['MAIL_FROM'] ?? 'no-reply@localhost'
    ],
    'razorpay' => [
        'key_id' => $env['RAZORPAY_KEY_ID'] ?? '',
        'key_secret' => $env['RAZORPAY_KEY_SECRET'] ?? ''
    ],
    'upi' => [
        'vpa' => $env['UPI_VPA'] ?? '',
        'qr_image' => $env['UPI_QR_IMAGE'] ?? ''
    ]
];
