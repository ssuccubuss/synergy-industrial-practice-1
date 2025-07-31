<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
redirectIfNotLoggedIn();

$db = new Database();
$conn = $db->getConnection();

// Получение информации о пользователе
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Получение истории покупок
$purchases = [];
$query = "SELECT p.*, b.title, b.author 
          FROM purchases p 
          JOIN books b ON p.book_id = b.id 
          WHERE p.user_id = ? 
          ORDER BY p.purchase_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$purchases = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Получение активных аренд
$rentals = [];
$query = "SELECT r.*, b.title, b.author 
          FROM rentals r 
          JOIN books b ON r.book_id = b.id 
          WHERE r.user_id = ? AND r.status = 'active'
          ORDER BY r.end_date ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$rentals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - BookStore</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="profile-container">
        <?php if (isset($_GET['success']) && $_GET['success'] === 'order_complete'): ?>
            <div class="success-message">
                Thank you for your order! Your books are now available.
            </div>
        <?php endif; ?>
        
        <h1>Your Profile</h1>
        
        <div class="profile-sections">
            <div class="profile-sidebar">
                <div class="user-info">
                    <div class="avatar">
                        <img src="assets/images/default-avatar.png" alt="User Avatar">
                    </div>
                    <h2><?= htmlspecialchars($user['username']) ?></h2>
                    <p>Member since: <?= date('F Y', strtotime($user['created_at'])) ?></p>
                </div>
                
                <nav class="profile-nav">
                    <ul>
                        <li><a href="#purchases" class="active">Your Purchases</a></li>
                        <li><a href="#rentals">Your Rentals</a></li>
                        <li><a href="#settings">Account Settings</a></li>
                    </ul>
                </nav>
            </div>
            
            <div class="profile-content">
                <section id="purchases" class="profile-section active">
                    <h2>Your Purchases</h2>
                    
                    <?php if (empty($purchases)): ?>
                        <p>You haven't purchased any books yet.</p>
                    <?php else: ?>
                        <table class="purchases-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Book</th>
                                    <th>Author</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchases as $purchase): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($purchase['purchase_date'])) ?></td>
                                    <td><?= htmlspecialchars($purchase['title']) ?></td>
                                    <td><?= htmlspecialchars($purchase['author']) ?></td>
                                    <td>$<?= number_format($purchase['price'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>
                
                <section id="rentals" class="profile-section">
                    <h2>Your Active Rentals</h2>
                    
                    <?php if (empty($rentals)): ?>
                        <p>You don't have any active rentals.</p>
                    <?php else: ?>
                        <table class="rentals-table">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Author</th>
                                    <th>Rental Period</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rentals as $rental): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rental['title']) ?></td>
                                    <td><?= htmlspecialchars($rental['author']) ?></td>
                                    <td>
                                        <?= match($rental['rental_type']) {
                                            '2weeks' => '2 Weeks',
                                            'month' => '1 Month',
                                            '3months' => '3 Months'
                                        } ?>
                                    </td>
                                    <td <?= strtotime($rental['end_date']) < time() ? 'class="overdue"' : '' ?>>
                                        <?= date('M d, Y', strtotime($rental['end_date'])) ?>
                                        <?php if (strtotime($rental['end_date']) < time()): ?>
                                            (Overdue)
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-<?= $rental['status'] ?>">
                                            <?= ucfirst($rental['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>
                
                <section id="settings" class="profile-section">
                    <h2>Account Settings</h2>
                    
                    <form class="settings-form" method="post" action="update_profile.php">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" 
                                   value="<?= htmlspecialchars($user['username']) ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </section>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/scripts.js"></script>
    <script>
        // Переключение между разделами профиля
        document.querySelectorAll('.profile-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Удаляем активный класс у всех ссылок и разделов
                document.querySelectorAll('.profile-nav a').forEach(el => el.classList.remove('active'));
                document.querySelectorAll('.profile-section').forEach(el => el.classList.remove('active'));
                
                // Добавляем активный класс к текущей ссылке и разделу
                this.classList.add('active');
                const target = this.getAttribute('href');
                document.querySelector(target).classList.add('active');
            });
        });
    </script>
</body>
</html>