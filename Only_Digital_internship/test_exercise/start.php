<?php
declare(strict_types=1); 
if (!defined('APP_PAGE')) { http_response_code(404); exit; } 
require __DIR__ . '/lib.php'; 
$config = require __DIR__ . '/config.php'; 
ini_set('display_errors', '0'); 
header('Content-Type: text/html; charset=utf-8'); 
header('Cache-Control: no-store'); 
$post = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($post && input('action') === 'logout') {
    authClear($config);
    redirect('/index.php');
}
$db = new PDO($config['dsn'], $config['db_user'], $config['db_password'], [ 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    PDO::ATTR_EMULATE_PREPARES => false, 
]); 
$user = null; 
$user = null;
$uid = authRead($config);
if ($uid !== null) {
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$uid]);
    $user = $stmt->fetch() ?: null;
}
if (APP_PAGE === 'profile' && !$user) { redirect('/index.php'); } 
if (APP_PAGE === 'auth' && $user) { redirect('/profile.php'); } 
$errors = []; 
$notice = $_GET['notice'] ?? '';
