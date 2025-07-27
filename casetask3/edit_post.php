<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$postId = $_GET['id'];
$post = getPostById($postId);

if (!$post || $post['user_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

// Получаем текущие теги поста
$currentTags = getPostTags($postId);
$currentTagsString = implode(', ', array_column($currentTags, 'name'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $visibility = $_POST['visibility'];
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
    
    if (empty($title) || empty($content)) {
        $error = 'Заголовок и содержимое поста обязательны';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Обновляем пост
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, visibility = ? WHERE id = ?");
            $stmt->execute([$title, $content, $visibility, $postId]);
            
            // Удаляем все текущие теги поста
            $stmt = $pdo->prepare("DELETE FROM post_tags WHERE post_id = ?");
            $stmt->execute([$postId]);
            
            // Добавляем новые теги
            if (!empty($tags)) {
                $tagNames = array_map('trim', explode(',', $tags));
                
                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) continue;
                    
                    // Проверяем, существует ли тег
                    $stmt = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
                    $stmt->execute([$tagName]);
                    $tag = $stmt->fetch();
                    
                    if (!$tag) {
                        // Создаем новый тег
                        $stmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
                        $stmt->execute([$tagName]);
                        $tagId = $pdo->lastInsertId();
                    } else {
                        $tagId = $tag['id'];
                    }
                    
                    // Связываем тег с постом
                    $stmt = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
                    $stmt->execute([$postId, $tagId]);
                }
            }
            
            $pdo->commit();
            $success = 'Пост успешно обновлен!';
            $post = getPostById($postId); // Обновляем данные поста
            $currentTags = getPostTags($postId); // Обновляем теги
            $currentTagsString = implode(', ', array_column($currentTags, 'name'));
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Ошибка при обновлении поста: ' . $e->getMessage();
        }
    }
}

if (isset($_POST['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$postId, $_SESSION['user_id']]);
        header("Location: dashboard.php");
        exit();
    } catch (Exception $e) {
        $error = 'Ошибка при удалении поста: ' . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<h2>Редактировать пост</h2>

<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<form method="post" action="edit_post.php?id=<?php echo $postId; ?>">
    <div>
        <label for="title">Заголовок:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
    </div>
    <div>
        <label for="content">Содержимое:</label>
        <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    </div>
    <div>
        <label for="visibility">Видимость:</label>
        <select id="visibility" name="visibility">
            <option value="public" <?php echo $post['visibility'] == 'public' ? 'selected' : ''; ?>>Публичный</option>
            <option value="private" <?php echo $post['visibility'] == 'private' ? 'selected' : ''; ?>>Приватный (только я)</option>
            <option value="request" <?php echo $post['visibility'] == 'request' ? 'selected' : ''; ?>>Только по запросу</option>
        </select>
    </div>
    <div>
        <label for="tags">Теги (через запятую):</label>
        <input type="text" id="tags" name="tags" value="<?php echo htmlspecialchars($currentTagsString); ?>" placeholder="например, программирование, php, блог">
    </div>
    <button type="submit">Обновить пост</button>
</form>

<form method="post" action="edit_post.php?id=<?php echo $postId; ?>" onsubmit="return confirm('Вы уверены, что хотите удалить этот пост?');">
    <input type="hidden" name="delete" value="1">
    <button type="submit" class="delete">Удалить пост</button>
</form>

<?php require_once 'includes/footer.php'; ?>