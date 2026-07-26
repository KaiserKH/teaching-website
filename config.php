<?php
// Copy this file to config.php and set DB credentials and site URL
return [
    'db' => [
        'host' => '127.0.0.1',
        'dbname' => 'teaching_website',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4'
    ],
    'base_url' => '/',
    'uploads_dir' => __DIR__ . '/uploads',
    'max_upload_size' => 10 * 1024 * 1024 // 10MB
];
