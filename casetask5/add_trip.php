<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
    $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
    $description = trim($_POST['description']);
    $cost = isset($_POST['cost']) ? floatval($_POST['cost']) : null;
    $heritage_sites = trim($_POST['heritage_sites']);
    $visit_places = trim($_POST['visit_places']);
    $comfort_rating = isset($_POST['comfort_rating']) ? intval($_POST['comfort_rating']) : null;
    
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'images/';
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO trips (user_id, title, location, latitude, longitude, description, cost, heritage_sites, visit_places, comfort_rating, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $location,
            $latitude,
            $longitude,
            $description,
            $cost,
            $heritage_sites,
            $visit_places,
            $comfort_rating,
            $image_path
        ]);
        
        header('Location: view_trips.php');
        exit;
    } catch (PDOException $e) {
        $error = "Ошибка при добавлении путешествия: " . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <h2>Добавить новое путешествие</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Название:</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="location">Местоположение:</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>
        <div class="form-group">
            <label for="latitude">Широта (необязательно):</label>
            <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude">
        </div>
        <div class="form-group">
            <label for="longitude">Долгота (необязательно):</label>
            <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude">
        </div>
        <div class="form-group">
            <label for="description">Описание:</label>
            <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="cost">Стоимость путешествия (необязательно):</label>
            <input type="number" step="0.01" class="form-control" id="cost" name="cost">
        </div>
        <div class="form-group">
            <label for="heritage_sites">Места культурного наследия (необязательно):</label>
            <textarea class="form-control" id="heritage_sites" name="heritage_sites" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="visit_places">Места для посещения (необязательно):</label>
            <textarea class="form-control" id="visit_places" name="visit_places" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="comfort_rating">Оценка удобства (1-10, необязательно):</label>
            <input type="number" min="1" max="10" class="form-control" id="comfort_rating" name="comfort_rating">
        </div>
        <div class="form-group">
            <label for="image">Изображение (необязательно):</label>
            <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
        </div>
        <button type="button" class="btn btn-info" onclick="getLocation()">Определить мое местоположение</button>
        <button type="submit" class="btn btn-primary">Добавить путешествие</button>
    </form>
</div>

<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
            },
            function(error) {
                alert('Ошибка получения местоположения: ' + error.message);
            }
        );
    } else {
        alert('Геолокация не поддерживается вашим браузером');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>