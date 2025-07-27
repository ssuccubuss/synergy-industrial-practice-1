<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (loginUser($username, $password)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Неверное имя пользователя или пароль';
    }
}

require_once 'includes/header.php';
?>

<h2>Вход</h2>
<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" action="login.php">
    <div>
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Войти</button>
</form>
<p>Еще не зарегистрированы? <a href="register.php">Создайте аккаунт</a></p>

<?php require_once 'includes/footer.php'; ?>