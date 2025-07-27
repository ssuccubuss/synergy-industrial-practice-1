<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

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
            
            // Создаем пост
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, visibility) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $content, $visibility]);
            $postId = $pdo->lastInsertId();
            
            // Обрабатываем теги
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
            $success = 'Пост успешно создан!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Ошибка при создании поста: ' . $e->getMessage();
        }
    }
}

require_once 'includes/header.php';
?>

<h2>Создать новый пост</h2>

<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo $success; ?></div>
    <p><a href="create_post.php">Создать еще один пост</a> или <a href="dashboard.php">вернуться в личный кабинет</a></p>
<?php else: ?>
    <form method="post" action="create_post.php">
        <div>
            <label for="title">Заголовок:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div>
            <label for="content">Содержимое:</label>
            <textarea id="content" name="content" rows="10" required></textarea>
        </div>
        <div>
            <label for="visibility">Видимость:</label>
            <select id="visibility" name="visibility">
                <option value="public">Публичный</option>
                <option value="private">Приватный (только я)</option>
                <option value="request">Только по запросу</option>
            </select>
        </div>
        <div>
            <label for="tags">Теги (через запятую):</label>
            <input type="text" id="tags" name="tags" placeholder="например, программирование, php, блог">
        </div>
        <button type="submit">Опубликовать</button>
    </form>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>