<?php
declare(strict_types=1);
return [
    'dsn' => 'mysql:host=127.0.0.1;dbname=auth_plaintext;charset=utf8mb4',
    'db_user' => 'auth_plaintext',
    'db_password' => '',
    'captcha_site_key' => '',
    'captcha_secret' => '',
    'captcha_hosts' => ['testexercise.teambtest.ru'],
    'secure_cookie' => false, 
    'auth_cookie' => 'auth_uid',
];
