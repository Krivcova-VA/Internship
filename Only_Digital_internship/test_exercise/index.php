<?php 
declare(strict_types=1); 
define('APP_PAGE', 'auth'); 
require __DIR__ . '/bootstrap.php'; 
$register = ($_GET['form'] ?? '') === 'register'; 
if ($post && !$register) { 
    $identity = identityValue(input('identity')); 
    $password = input('password'); 
    if ($identity === null) { $errors[] = 'Введите корректный телефон или email.'; } 
    if ($password === '' || strlen($password) > 72 || str_contains($password, "\0")) { $errors[] = 'Введите корректный пароль, не более 36 символов'; }
    if (!$errors && !captchaValid($config, input('smart-token'))) { $errors[] = 'Пройдите капчу заново. Если она недоступна, попробуйте позже.'; }
    if (!$errors) { 
        $sql = $identity[0] === 'email' ? 'SELECT * FROM users WHERE email = ?' : 'SELECT * FROM users WHERE phone = ?';
        $stmt = $db->prepare($sql); 
        $stmt->execute([$identity[1]]); 
        $found = $stmt->fetch();
        if ($found && checkPassword($password, $found['password'])) {
            signIn((int)$found['id'], (int)$found['session_version']); 
            redirect('/profile.php'); 
        } 
        $errors[] = 'Неверный телефон/email или пароль.'; 
    } 
}
if ($post && $register) { 
    [$data, $errors] = personalData(); 
    $errors = array_merge($errors, passwordErrors(input('password'), input('password_repeat'))); 
    if (!$errors && !captchaValid($config, input('smart-token'))) {
    $errors[] = 'Пройдите капчу заново. Если она недоступна, попробуйте позже.';
    }
    if (!$errors) { $errors = duplicates($db, $data); } 
    if (!$errors) { 
        try {
            $stmt = $db->prepare('INSERT INTO users (name,login,phone,email,password) VALUES (?,?,?,?,?)'); 
            $stmt->execute([$data['name'], $data['login'], $data['phone'], $data['email'], encodePassword(input('password'))]);
            $newId = (int)$db->lastInsertId(); 
            signIn($newId, 1); 
            $_SESSION['notice'] = 'Регистрация завершена. Добро пожаловать!'; 
            redirect('/profile.php'); 
        } catch (PDOException $error) { 
            if ((int)($error->errorInfo[1] ?? 0) !== 1062) { throw $error; } 
            $errors = duplicates($db, $data); 
            if (!$errors) { $errors[] = 'Логин, почта или телефон уже используются.'; }
        } 
    } 
} 
?> 
<!doctype html>
<html lang="ru"> 
<head> 
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход и регистрация</title> 
</head> 
<body> 
<nav>
    <a href="/index.php?form=register">Регистрация</a>
    | 
    <a href="/index.php">Вход</a>
    | 
    <a href="/index.php">Профиль</a>
</nav>

<?php foreach ($errors as $error): ?>
    <p role="alert"><?= e($error) ?></p>
<?php endforeach; ?>
<?php if ($register): ?>

<h1>Регистрация</h1>

<form method="post" action="/index.php?form=register"> 
    <p>
        <label>Имя</label><br>
        <input name="name" type="text" value="<?= e(input('name')) ?>" maxlength="100" autocomplete="name" required>
    </p>
    <p>
        <label>Логин</label><br>
        <input name="login" type="text" value="<?= e(input('login')) ?>" minlength="3" maxlength="32" pattern="[a-zA-Z0-9_]{3,32}" autocomplete="username" required>
    </p>
    <p>
        <label>Телефон</label><br>
        <input name="phone" type="tel" value="<?= e(input('phone')) ?>" pattern="(?:\+7|8)(?:[0-9]{10}| \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2})" placeholder="+79991234567" autocomplete="tel" required>
    </p>
    <p>
        <label>Почта</label><br>
        <input name="email" type="email" value="<?= e(input('email')) ?>" maxlength="254" autocomplete="email" required>
    </p>
    <p>
        <label>Пароль</label><br>
        <input name="password" type="password" minlength="12" maxlength="72" autocomplete="new-password" required>
    </p>
    <p>
        <label>Повтор пароля</label><br>
        <input name="password_repeat" type="password" minlength="12" maxlength="72" autocomplete="new-password" required>
    </p>
    <p>Пароль: 12–36 символов. Телефон: +79991234567 или +7 (999) 123-45-67.</p>
    
    <div class="smart-captcha" data-sitekey="<?= e($config['captcha_site_key']) ?>" style="height: 100px"></div>

    <script src="https://smartcaptcha.cloud.yandex.ru/captcha.js" defer></script>
    <button type="submit">Зарегистрироваться</button> 
</form> 
<?php else:?>


<h1>Вход</h1> 
<form method="post" action="/index.php">
    <p>
        <label>Телефон или email</label><br>
        <input name="identity" type="text" value="<?= e(input('identity')) ?>" pattern="(?:(?:\+7|8)(?:[0-9]{10}| \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2})|[^\s@]+@[^\s@]+\.[^\s@]+)" placeholder="+79991234567 или user@example.com" autocomplete="username" required>
    </p>
    <p>
        <label>Пароль<br>
            <input name="password" type="password" required autocomplete="current-password">
        </label>
    </p> 
    <div class="smart-captcha" data-sitekey="<?= e($config['captcha_site_key']) ?>" style="height:100px"></div> 
    <script src="https://smartcaptcha.cloud.yandex.ru/captcha.js" defer></script> 
    <button type="submit">Войти</button>
</form>
<?php endif; ?>
</body>
</html>
