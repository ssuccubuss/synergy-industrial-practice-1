<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    $stmt = $pdo->query("SELECT t.*, u.username FROM trips t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
    $trips = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Ошибка при получении путешествий: " . $e->getMessage());
}
?>

<div class="container">
    <h2>Все путешествия</h2>
    <?php if (empty($trips)): ?>
        <p>Пока нет ни одного путешествия.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($trips as $trip): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <?php if ($trip['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($trip['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($trip['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($trip['title']); ?></h5>
                            <p class="card-text"><small class="text-muted">Автор: <?php echo htmlspecialchars($trip['username']); ?></small></p>
                            <p class="card-text"><?php echo htmlspecialchars(substr($trip['description'], 0, 100)); ?>...</p>
                            <a href="trip_detail.php?id=<?php echo $trip['id']; ?>" class="btn btn-primary">Подробнее</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>