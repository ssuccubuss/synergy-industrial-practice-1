<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$postId = $_GET['id'];
$post = getPostById($postId);

if (!$post) {
    header("Location: index.php");
    exit();
}

// Проверка видимости поста
$canView = false;
if (isLoggedIn()) {
    if ($post['visibility'] == 'public' || 
        $post['visibility'] == 'request' || 
        $post['user_id'] == $_SESSION['user_id'] || 
        isSubscribed($_SESSION['user_id'], $post['user_id'])) {
        $canView = true;
    }
} else {
    $canView = ($post['visibility'] == 'public');
}

if (!$canView) {
    if ($post['visibility'] == 'request' && isLoggedIn()) {
        // Можно показать кнопку "запросить доступ"
        // В реальном приложении нужно реализовать систему запросов
    }
    header("Location: index.php");
    exit();
}

// Обработка комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isLoggedIn()) {
    $comment = trim($_POST['comment']);
    
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$postId, $_SESSION['user_id'], $comment]);
    }
}

$tags = getPostTags($postId);
$comments = getCommentsForPost($postId);

require_once 'includes/header.php';
?>

<div class="post">
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <small>Автор: <?php echo htmlspecialchars($post['username']); ?></small>
    <small>Опубликовано: <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></small>
    
    <?php if (!empty($tags)): ?>
        <div class="tags">
            Теги: 
            <?php foreach ($tags as $tag): ?>
                <span class="tag"><?php echo htmlspecialchars($tag['name']); ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="content">
        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
    </div>
    
    <?php if (isLoggedIn() && $post['user_id'] == $_SESSION['user_id']): ?>
        <p><a href="edit_post.php?id=<?php echo $postId; ?>">Редактировать</a></p>
    <?php endif; ?>
</div>

<div class="comments">
    <h3>Комментарии (<?php echo count($comments); ?>)</h3>
    
    <?php if (isLoggedIn()): ?>
        <form method="post" action="view_post.php?id=<?php echo $postId; ?>">
            <textarea name="comment" placeholder="Ваш комментарий..." required></textarea>
            <button type="submit">Отправить</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Войдите</a>, чтобы оставить комментарий</p>
    <?php endif; ?>
    
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
            <small><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></small>
            <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>