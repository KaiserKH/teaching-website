<?php
require_once __DIR__ . '/../functions.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_NAME']), '/');
$path = '/' . trim(str_replace('/public', '', $uri), '/');
if ($path === '/' || $path === '') $path = '/home';

// simple routing map
$routes = [
    '/home' => __DIR__ . '/pages/home.php',
    '/about' => __DIR__ . '/pages/about.php',
    '/courses' => __DIR__ . '/pages/courses.php',
    '/schedule' => __DIR__ . '/pages/schedule.php',
    '/gallery' => __DIR__ . '/pages/gallery.php',
    '/blog' => __DIR__ . '/pages/blog.php',
    '/blog/post' => __DIR__ . '/pages/blog_post.php',
    '/admission' => __DIR__ . '/pages/admission.php',
    '/contact' => __DIR__ . '/pages/contact.php',
    '/login' => __DIR__ . '/pages/login.php',
    '/admin/login' => __DIR__ . '/pages/admin_login.php',
    '/student/dashboard' => __DIR__ . '/pages/student/dashboard.php',
    '/admin/dashboard' => __DIR__ . '/pages/admin/dashboard.php',
    '/admin/admissions' => __DIR__ . '/pages/admin/admissions.php',
    '/admin/approve' => __DIR__ . '/pages/admin/approve_admission.php'
];

if (isset($routes[$path])) {
    require $routes[$path];
} else {
    // try slug routes for blog
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>The requested URL {$path} was not found.</p>";
}
