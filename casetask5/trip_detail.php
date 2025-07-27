<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: view_trips.php');
    exit;
}

$trip_id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT t.*, u.username FROM trips t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
    $stmt->execute([$trip_id]);
    $trip = $stmt->fetch();
    
    if (!$trip) {
        header('Location: view_trips.php');
        exit;
    }
} catch (PDOException $e) {
    die("Ошибка при получении путешествия: " . $e->getMessage());
}

require_once 'includes/header.php';
?>

<div class="container">
    <h2><?php echo htmlspecialchars($trip['title']); ?></h2>
    <p><small class="text-muted">Автор: <?php echo htmlspecialchars($trip['username']); ?></small></p>
    
    <?php if ($trip['image_path']): ?>
        <img src="<?php echo htmlspecialchars($trip['image_path']); ?>" class="img-fluid mb-3" alt="<?php echo htmlspecialchars($trip['title']); ?>">
    <?php endif; ?>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <p><strong>Местоположение:</strong> <?php echo htmlspecialchars($trip['location']); ?></p>
            <?php if ($trip['latitude'] && $trip['longitude']): ?>
                <div id="map" style="height: 400px; width: 100%;"></div>
                <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
                <script type="text/javascript">
                    ymaps.ready(init);
                    
                    function init() {
                        var myMap = new ymaps.Map("map", {
                            center: [<?php echo $trip['latitude']; ?>, <?php echo $trip['longitude']; ?>],
                            zoom: 12,
                            controls: ['zoomControl', 'typeSelector', 'fullscreenControl']
                        });
                        
                        var myPlacemark = new ymaps.Placemark([<?php echo $trip['latitude']; ?>, <?php echo $trip['longitude']; ?>], {
                            hintContent: "<?php echo htmlspecialchars($trip['location']); ?>",
                            balloonContent: `
                                <h4><?php echo htmlspecialchars($trip['title']); ?></h4>
                                <p><?php echo htmlspecialchars($trip['location']); ?></p>
                                <p><?php echo nl2br(htmlspecialchars(substr($trip['description'], 0, 150))); ?>...</p>
                            `
                        }, {
                            preset: 'islands#blueDotIcon'
                        });
                        
                        myMap.geoObjects.add(myPlacemark);
                        myPlacemark.balloon.open();
                    }
                </script>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php if ($trip['cost']): ?>
                <p><strong>Стоимость:</strong> <?php echo number_format($trip['cost'], 2); ?> руб.</p>
            <?php endif; ?>
            
            <?php if ($trip['comfort_rating']): ?>
                <p><strong>Оценка удобства:</strong> <?php echo $trip['comfort_rating']; ?>/10</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="mb-3">
        <h4>Описание</h4>
        <p><?php echo nl2br(htmlspecialchars($trip['description'])); ?></p>
    </div>
    
    <?php if ($trip['heritage_sites']): ?>
        <div class="mb-3">
            <h4>Места культурного наследия</h4>
            <p><?php echo nl2br(htmlspecialchars($trip['heritage_sites'])); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if ($trip['visit_places']): ?>
        <div class="mb-3">
            <h4>Места для посещения</h4>
            <p><?php echo nl2br(htmlspecialchars($trip['visit_places'])); ?></p>
        </div>
    <?php endif; ?>
    
    <a href="view_trips.php" class="btn btn-secondary">Назад к списку</a>
</div>

<?php require_once 'includes/footer.php'; ?>