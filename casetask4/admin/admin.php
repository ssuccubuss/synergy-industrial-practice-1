<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Проверка прав администратора
if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookStore</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-panel">
        <header class="admin-header">
            <div class="container">
                <h1><i class="fas fa-book-open"></i> BookStore Admin Panel</h1>
                <nav class="admin-nav">
                    <ul>
                        <li><a href="admin.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="books_management.php"><i class="fas fa-book"></i> Manage Books</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        
        <main class="admin-main">
            <div class="container">
                <section class="admin-stats">
                    <h2><i class="fas fa-chart-bar"></i> Statistics</h2>
                    <?php
                    // Получаем статистику
                    $books_count = $conn->query("SELECT COUNT(*) FROM books")->fetch_row()[0];
                    $users_count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
                    $active_rentals = $conn->query("SELECT COUNT(*) FROM rentals WHERE status = 'active'")->fetch_row()[0];
                    ?>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Total Books</h3>
                            <p><?= $books_count ?></p>
                            <i class="fas fa-book stat-icon"></i>
                        </div>
                        <div class="stat-card">
                            <h3>Total Users</h3>
                            <p><?= $users_count ?></p>
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                        <div class="stat-card">
                            <h3>Active Rentals</h3>
                            <p><?= $active_rentals ?></p>
                            <i class="fas fa-calendar-check stat-icon"></i>
                        </div>
                    </div>
                </section>
                
                <section class="recent-activity">
                    <h2><i class="fas fa-clock"></i> Recent Activity</h2>
                    <?php
                    $query = "SELECT r.*, u.username, b.title 
                              FROM rentals r 
                              JOIN users u ON r.user_id = u.id 
                              JOIN books b ON r.book_id = b.id 
                              ORDER BY r.start_date DESC LIMIT 5";
                    $result = $conn->query($query);
                    ?>
                    
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Book</th>
                                    <th>Rental Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($rental = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rental['username']) ?></td>
                                    <td><?= htmlspecialchars($rental['title']) ?></td>
                                    <td><?= str_replace(['_', '2weeks'], [' ', '2 weeks'], $rental['rental_type']) ?></td>
                                    <td><?= date('M d, Y', strtotime($rental['start_date'])) ?></td>
                                    <td class="<?= strtotime($rental['end_date']) < time() ? 'text-danger' : '' ?>">
                                        <?= date('M d, Y', strtotime($rental['end_date'])) ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $rental['status'] ?>">
                                            <?= ucfirst($rental['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/scripts.js"></script>
</body>
</html>