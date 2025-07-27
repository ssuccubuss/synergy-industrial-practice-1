<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_GET['id'];
$user = getUserById($userId);

if (!$user) {
    header("Location: index.php");
    exit();
}

$isOwnProfile = isLoggedIn() && $_SESSION['user_id'] == $userId;
$posts = getPostsByUser($userId, isLoggedIn() ? $_SESSION['user_id'] : null);

require_once 'includes/header.php';
?>

<h2>Профиль пользователя: <?php echo htmlspecialchars($user['username']); ?></h2>

<?php if (isLoggedIn() && !$isOwnProfile): ?>
    <?php if (isSubscribed($_SESSION['user_id'], $userId)): ?>
        <form method="post" action="subscriptions.php">
            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
            <input type="hidden" name="action" value="unsubscribe">
            <button type="submit">Отписаться</button>
        </form>
    <?php else: ?>
        <form method="post" action="subscriptions.php">
            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
            <input type="hidden" name="action" value="subscribe">
            <button type="submit">Подписаться</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

<h3>Посты пользователя:</h3>
<?php if (count($posts) > 0): ?>
    <div class="posts">
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h4><a href="view_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h4>
                <small>Опубликовано: <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></small>
                <small>Статус: <?php echo $post['visibility']; ?></small>
                <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 150) . (strlen($post['content']) > 150 ? '...' : ''))); ?></p>
                <a href="view_post.php?id=<?php echo $post['id']; ?>">Читать далее</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>У пользователя пока нет постов.</p>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>