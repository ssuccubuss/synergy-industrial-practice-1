<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$posts = getFeedPosts($_SESSION['user_id']);

require_once 'includes/header.php';
?>

<h2>Лента подписок</h2>

<?php if (count($posts) > 0): ?>
    <div class="posts">
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h3><a href="view_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                <small>Автор: <?php echo htmlspecialchars($post['username']); ?></small>
                <small>Опубликовано: <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></small>
                <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200) . (strlen($post['content']) > 200 ? '...' : ''))); ?></p>
                <a href="view_post.php?id=<?php echo $post['id']; ?>">Читать далее</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>В вашей ленте пока нет постов. <a href="subscriptions.php">Подпишитесь на других пользователей</a>, чтобы видеть их публикации.</p>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>