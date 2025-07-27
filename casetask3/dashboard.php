<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user = getUserById($_SESSION['user_id']);
$posts = getPostsByUser($_SESSION['user_id'], $_SESSION['user_id']);

require_once 'includes/header.php';
?>

<h2>Добро пожаловать, <?php echo htmlspecialchars($user['username']); ?>!</h2>

<h3>Ваши посты:</h3>
<?php if (count($posts) > 0): ?>
    <div class="posts">
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h4><a href="view_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h4>
                <small>Опубликовано: <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></small>
                <small>Статус: <?php echo $post['visibility']; ?></small>
                <p><a href="edit_post.php?id=<?php echo $post['id']; ?>">Редактировать</a></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>У вас еще нет постов. <a href="create_post.php">Создайте первый пост</a></p>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>