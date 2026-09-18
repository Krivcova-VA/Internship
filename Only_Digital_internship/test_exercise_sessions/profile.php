<?php
declare(strict_types=1);
define('APP_PAGE', 'profile'); 
require __DIR__ . '/start.php'; 
if ($post) { 
    [$data, $errors] = personalData($user['login']);
    if (!checkPassword(input('current_password'), $user['password'])) { $errors[] = 'Текущий пароль неверен.'; } 
    $changePassword = input('password') !== '' || input('password_repeat') !== '';
    if ($changePassword) { $errors = array_merge($errors, passwordErrors(input('password'), input('password_repeat'))); }
    if (!$errors) { $errors = duplicates($db, $data, (int)$user['id']); } 
    if (!$errors) { 
        $password = $user['password']; 
        if ($changePassword) { $password = encodePassword(input('password')); }
        try { 
            $stmt = $db->prepare('UPDATE users SET name=?,phone=?,email=?,password=?,session_version=session_version+1 WHERE id=? AND session_version=?');
            $stmt->execute([$data['name'], $data['phone'], $data['email'], $password, $user['id'], $user['session_version']]);
            redirect('/profile.php?notice=' . urlencode('Профиль сохранён.')); 
        } catch (PDOException $error) { 
            if ((int)($error->errorInfo[1] ?? 0) !== 1062) { throw $error; } 
            $errors = duplicates($db, $data, (int)$user['id']); 
            if (!$errors) { $errors[] = 'Почта или телефон уже используются.'; } 
        }
    }
} 
?> 
<!doctype html>
<html lang="ru"> 
<head> 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Профиль</title> 
</head> 
<body> 
<nav>
    <a href="/profile.php">Профиль</a>
</nav> 

<h1>Профиль</h1> 
<?php if ($notice !== ''):?>
    <p role="status"><?= e($notice) ?></p>
<?php endif;?>

<?php foreach ($errors as $error):?>
    <p role="alert"><?= e($error) ?></p>
<?php endforeach;?>
<p>Логин: <?= e($user['login']) ?></p>

<form method="post" action="/profile.php">
    <p>
        <label>Имя<br>
            <input name="name" type="text" value="<?= e($post ? input('name') : $user['name']) ?>" required>
        </label>
    </p>
    <p>
        <label> Телефон<br>
            <input name="phone" type="tel" value="<?= e($post ? input('phone') : $user['phone']) ?>" required>
        </label>
    </p>
    <p>
        <label>  Почта<br>
            <input name="email" type="email" value="<?= e($post ? input('email') : $user['email']) ?>" required>
        </label>
    </p>
    <p>
        <label> Текущий пароль<br>
            <input name="current_password" type="password" required>
        </label>
    </p>
    <p>
        <label> Новый пароль<br>
            <input name="password" type="password">
        </label>
    </p>
    <p>
        <label> Повтор нового пароля<br>
            <input name="password_repeat" type="password">
        </label>
    </p>
    <p>Новый пароль: 12–36 символов</p>
    <button type="submit">Сохранить</button>
</form>

<form method="post" action="/index.php">
    <input type="hidden" name="action" value="logout">
    <button type="submit">Выйти</button>
</form>
</body>
</html>
