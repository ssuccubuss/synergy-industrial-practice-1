<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$db = new Database();
$conn = $db->getConnection();

// Получаем популярные книги
$query = "SELECT * FROM books ORDER BY RAND() LIMIT 6";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore - Home</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="hero">
            <h1>Welcome to BookStore</h1>
            <p>Discover your next favorite book</p>
        </section>
        
        <section class="featured-books">
            <h2>Featured Books</h2>
            <div class="books-grid">
                <?php while($book = $result->fetch_assoc()): ?>
                <div class="book-card">
                    <img src="assets/images/<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p><?= htmlspecialchars($book['author']) ?></p>
                    <a href="books.php?action=view&id=<?= $book['id'] ?>" class="btn">View Details</a>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/scripts.js"></script>
</body>
</html>