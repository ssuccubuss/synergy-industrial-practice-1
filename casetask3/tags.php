<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Функция для получения имени тега
function getTagNameById($tagId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT name FROM tags WHERE id = ?");
    $stmt->execute([$tagId]);
    return $stmt->fetchColumn();
}

// Получаем все теги с количеством постов
$tags = $pdo->query("
    SELECT t.id, t.name, COUNT(pt.post_id) as post_count
    FROM tags t
    LEFT JOIN post_tags pt ON t.id = pt.tag_id
    LEFT JOIN posts p ON pt.post_id = p.id AND p.visibility = 'public'
    GROUP BY t.id
    ORDER BY t.name
")->fetchAll(PDO::FETCH_ASSOC);

// Обработка выбора тега
$selectedTagId = isset($_GET['tag_id']) ? (int)$_GET['tag_id'] : null;
$selectedTagName = $selectedTagId ? getTagNameById($selectedTagId) : null;

// Получаем посты
if ($selectedTagId) {
    $posts = $pdo->prepare("
        SELECT p.*, u.username
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN post_tags pt ON p.id = pt.post_id
        WHERE pt.tag_id = ? AND p.visibility = 'public'
        ORDER BY p.created_at DESC
    ")->execute([$selectedTagId])->fetchAll(PDO::FETCH_ASSOC);
} else {
    $posts = $pdo->query("
        SELECT p.*, u.username
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.visibility = 'public'
        ORDER BY p.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

require_once 'includes/header.php';
?>

<div class="content-container">
    <h2><?= $selectedTagName ? "Посты с тегом: " . htmlspecialchars($selectedTagName) : "Все публичные посты" ?></h2>

    <!-- Облако тегов -->
    <div class="tag-cloud">
        <a href="tags.php" class="tag <?= !$selectedTagId ? 'active' : '' ?>">Все теги</a>
        <?php foreach ($tags as $tag): ?>
            <a href="tags.php?tag_id=<?= $tag['id'] ?>" 
               class="tag <?= $selectedTagId == $tag['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($tag['name']) ?> (<?= $tag['post_count'] ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Список постов -->
    <div class="posts-list">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-item">
                    <h3><a href="view_post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
                    <div class="post-meta">
                        <span>Автор: <?= htmlspecialchars($post['username']) ?></span>
                        <span>Дата: <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></span>
                    </div>
                    <div class="post-excerpt">
                        <?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...
                    </div>
                    <div class="post-tags">
                        <?php foreach (getPostTags($post['id']) as $tag): ?>
                            <a href="tags.php?tag_id=<?= $tag['id'] ?>" class="post-tag">
                                <?= htmlspecialchars($tag['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-posts">
                <?= $selectedTagId ? "Нет публичных постов с выбранным тегом." : "Пока нет публичных постов." ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>