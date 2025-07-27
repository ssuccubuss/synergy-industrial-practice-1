<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой блог</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="<?php echo BASE_URL; ?>">Мой блог</a></h1>
            <nav>
                <ul>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?php echo BASE_URL; ?>dashboard.php">Личный кабинет</a></li>
                        <li><a href="<?php echo BASE_URL; ?>feed.php">Лента</a></li>
                        <li><a href="<?php echo BASE_URL; ?>create_post.php">Новый пост</a></li>
                        <li><a href="<?php echo BASE_URL; ?>subscriptions.php">Подписки</a></li>
                        <li><a href="<?php echo BASE_URL; ?>includes/logout.php">Выход</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>login.php">Вход</a></li>
                        <li><a href="<?php echo BASE_URL; ?>register.php">Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">