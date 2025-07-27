<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Подписка/отписка
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $targetUserId = (int)$_POST['user_id'];
    
    if ($_POST['action'] === 'subscribe') {
        try {
            $stmt = $pdo->prepare("INSERT INTO subscriptions (subscriber_id, subscribed_to_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $targetUserId]);
        } catch (PDOException $e) {
            // Игнорируем ошибку дублирования (уже подписан)
        }
    } elseif ($_POST['action'] === 'unsubscribe') {
        $stmt = $pdo->prepare("DELETE FROM subscriptions WHERE subscriber_id = ? AND subscribed_to_id = ?");
        $stmt->execute([$_SESSION['user_id'], $targetUserId]);
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Получаем всех пользователей, кроме текущего
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ? ORDER BY username");
$stmt->execute([$_SESSION['user_id']]);
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем подписки текущего пользователя
$stmt = $pdo->prepare("
    SELECT u.id, u.username 
    FROM users u
    JOIN subscriptions s ON u.id = s.subscribed_to_id
    WHERE s.subscriber_id = ?
    ORDER BY u.username
");
$stmt->execute([$_SESSION['user_id']]);
$subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subscriptionIds = array_column($subscriptions, 'id');

require_once 'includes/header.php';
?>

<h2>Управление подписками</h2>

<h3>Ваши подписки:</h3>
<?php if (count($subscriptions) > 0): ?>
    <ul class="subscription-list">
        <?php foreach ($subscriptions as $user): ?>
            <li>
                <a href="profile.php?id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></a>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <input type="hidden" name="action" value="unsubscribe">
                    <button type="submit">Отписаться</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Вы пока ни на кого не подписаны.</p>
<?php endif; ?>

<h3>Все пользователи:</h3>
<ul class="user-list">
    <?php foreach ($allUsers as $user): ?>
        <?php if (!in_array($user['id'], $subscriptionIds)): ?>
            <li>
                <a href="profile.php?id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></a>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <input type="hidden" name="action" value="subscribe">
                    <button type="submit">Подписаться</button>
                </form>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

<?php require_once 'includes/footer.php'; ?>