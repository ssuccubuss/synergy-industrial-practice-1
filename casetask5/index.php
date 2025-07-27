<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<div class="container">
    <h1>Добро пожаловать в Дневник путешествий</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Привет, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <a href="add_trip.php" class="btn btn-primary">Добавить путешествие</a>
        <a href="view_trips.php" class="btn btn-secondary">Посмотреть все путешествия</a>
    <?php else: ?>
        <p>Войдите или зарегистрируйтесь, чтобы начать вести дневник путешествий.</p>
        <a href="login.php" class="btn btn-primary">Войти</a>
        <a href="register.php" class="btn btn-secondary">Зарегистрироваться</a>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>