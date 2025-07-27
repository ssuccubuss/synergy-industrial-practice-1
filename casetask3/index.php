<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Получаем последние публичные посты
$stmt = $pdo->prepare("
    SELECT p.*, u.username 
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.visibility = 'public'
    ORDER BY p.created_at DESC
    LIMIT 10
");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<h1>Последние публичные посты</h1>

<?php if (count($posts) > 0): ?>
    <div class="posts">
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h3><a href="view_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                <small>Автор: <?php echo htmlspecialchars($post['username']); ?></small>
                <small>Опубликовано: <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></small>
                <div class="post-content">
                    <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200) . (strlen($post['content']) > 200 ? '...' : ''))); ?>
                </div>
                <a href="view_post.php?id=<?php echo $post['id']; ?>" class="read-more">Читать далее</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-posts">
        Пока нет публичных постов.
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>