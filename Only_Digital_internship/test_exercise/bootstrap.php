<?php
declare(strict_types=1); 
if (!defined('APP_PAGE')) { http_response_code(404); exit; } 
require __DIR__ . '/lib.php'; 
$config = require __DIR__ . '/config.php'; 
ini_set('display_errors', '0'); 
set_exception_handler(function (Throwable $error): void { 
    error_log('Auth failure: ' . get_class($error) . ' code=' . $error->getCode()); 
    http_response_code(500); 
    echo 'Внутренняя ошибка. Попробуйте позже.';
}); 
ini_set('session.use_strict_mode', '1'); 
ini_set('session.use_only_cookies', '1'); 
session_name($config['session_name']);
session_set_cookie_params(['httponly' => true, 'secure' => $config['secure_cookie'], 'samesite' => 'Lax', 'path' => '/']);
session_start(); 
header('Content-Type: text/html; charset=utf-8'); 
header('Cache-Control: no-store'); 
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY'); 
$post = $_SERVER['REQUEST_METHOD'] === 'POST';


if ($post && input('action') === 'logout') { 
    $_SESSION = []; 
    setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => '/', 'secure' => $config['secure_cookie'], 'httponly' => true, 'samesite' => 'Lax']); // Удаляем session cookie в браузере.
    session_destroy(); 
    redirect('/index.php'); 
}
if (APP_PAGE === 'profile' && empty($_SESSION['uid'])) { redirect('/index.php'); } 
$db = new PDO($config['dsn'], $config['db_user'], $config['db_password'], [ 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    PDO::ATTR_EMULATE_PREPARES => false, 
]); 
$user = null; 
if (isset($_SESSION['uid'])) { 
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?'); 
    $stmt->execute([$_SESSION['uid']]); 
    $user = $stmt->fetch() ?: null; 
    if (!$user || (int)$user['session_version'] !== ($_SESSION['version'] ?? 0)) { 
        unset($_SESSION['uid'], $_SESSION['version']); 
        $user = null;
    } 
} 
if (APP_PAGE === 'profile' && !$user) { redirect('/index.php'); } 
if (APP_PAGE === 'auth' && $user) { redirect('/profile.php'); } 
$errors = []; 
$notice = $_SESSION['notice'] ?? '';
unset($_SESSION['notice']); 
