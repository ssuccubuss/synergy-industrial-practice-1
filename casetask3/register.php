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
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать не менее 6 символов';
    } else {
        if (registerUser($username, $email, $password)) {
            header("Location: login.php");
            exit();
        } else {
            $error = 'Ошибка при регистрации. Возможно, имя пользователя или email уже заняты.';
        }
    }
}

require_once 'includes/header.php';
?>

<h2>Регистрация</h2>
<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="post" action="register.php">
    <div>
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <label for="confirm_password">Подтвердите пароль:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>
    <button type="submit">Зарегистрироваться</button>
</form>
<p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>

<?php require_once 'includes/footer.php'; ?>