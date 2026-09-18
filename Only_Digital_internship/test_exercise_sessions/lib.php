<?php
declare(strict_types=1);

const PHONE_PATTERN = '(?:\+7|8)(?:[0-9]{10}| \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2})';

function input(string $key): string {
    $value = $_POST[$key] ?? ''; 
    return is_string($value) ? $value : '';
} 

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); 
}
function redirect(string $url): never { 
    header('Location: ' . $url, true, 303);
    exit; 
}
/*проверка email*/
function emailValue(string $raw): ?string { 
    $email = strtolower(trim($raw));
    if (strlen($email) > 254) { return null; } 
    if (!preg_match('~\A[^\s@]+@[^\s@]+\.[^\s@]+\z~D', $email)) { return null; } 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { return null; } 
    return $email; 
}
/*проверка номера*/
function phoneValue(string $raw): ?string { 
    $phone = trim($raw); 
    if (!preg_match('~\A' . PHONE_PATTERN . '\z~D', $phone)) { return null; } 
    $digits = preg_replace('~[^0-9]~', '', $phone); 
    return '+7' . substr($digits, 1); 
}
/*определение типа авторизации: по телефону или email*/
function identityValue(string $raw): ?array {
    $email = emailValue($raw);
    if ($email !== null) { return ['email', $email]; } 
    $phone = phoneValue($raw);
    if ($phone !== null) { return ['phone', $phone]; }
    return null;
}
/*проверка пароля*/
function passwordErrors(string $password, string $repeat): array {
    $errors = [];
    if (strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0")) {
        $errors[] = 'Пароль должен содержать от 12 до 36 символов.'; 
    }
    if ($password !== $repeat) { $errors[] = 'Пароли не совпадают.'; }
    return $errors;
}
/*получение и проверка данных из форм*/
function personalData(?string $fixedLogin = null): array {
    $data = [];
    $data['name'] = trim(input('name')); 
    $data['login'] = $fixedLogin ?? strtolower(trim(input('login'))); 
    $data['email'] = emailValue(input('email')); 
    $data['phone'] = phoneValue(input('phone'));
    $errors = [];
    if (!preg_match('~\A[^\p{C}]{1,100}\z~u', $data['name'])) { $errors[] = 'Имя: 1–100 символов без управляющих символов.'; } 
    if (!preg_match('~\A[a-z0-9_]{3,32}\z~D', $data['login'])) { $errors[] = 'Логин: 3–32 латинские буквы, цифры или подчёркивание.'; }
    if ($data['email'] === null) { $errors[] = 'Некорректный email.'; } 
    if ($data['phone'] === null) { $errors[] = 'Некорректный телефон.'; } 
    return [$data, $errors]; 
}
/*проверка уникальности данных*/
function duplicates(PDO $db, array $data, int $except = 0): array {
    $errors = []; 
    foreach (['email' => 'Почта', 'phone' => 'Телефон', 'login' => 'Логин'] as $column => $label) { 
        $stmt = $db->prepare("SELECT id FROM users WHERE $column = ? AND id <> ? LIMIT 1"); 
        $stmt->execute([$data[$column], $except]);
        if ($stmt->fetch()) { $errors[] = "$label уже используется."; } 
    } 
    return $errors; 
} 
/*Yandex SmartCaptch*/
function captchaValid(array $config, string $token): bool { 
    if ($token === '' || strlen($token) > 8192) { return false; }
    $curl = curl_init('https://smartcaptcha.cloud.yandex.ru/validate'); 
    curl_setopt_array($curl, [ 
        CURLOPT_POST => true, 
        CURLOPT_RETURNTRANSFER => true, 
        CURLOPT_CONNECTTIMEOUT => 3, 
        CURLOPT_TIMEOUT => 8,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'], 
        CURLOPT_POSTFIELDS => http_build_query([
            'secret' => $config['captcha_secret'], 
            'token' => $token, 
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '', 
        ]), 
    ]); 
    $body = curl_exec($curl); 
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
    curl_close($curl); 
    if (!is_string($body) || $status !== 200) { return false; } 
    $result = json_decode($body, true); 
    if (!is_array($result) || ($result['status'] ?? '') !== 'ok') { return false; } 
    return in_array($result['host'] ?? '', $config['captcha_hosts'], true); 
} 

function encodePassword(string $password): string {
    return $password;
}
/*сравнение пароля с паролем из базы */
function checkPassword(
    string $password,
    string $stored
): bool {
    return hash_equals($stored, $password);
}
