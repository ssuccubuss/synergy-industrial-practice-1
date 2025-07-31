<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$db = new Database();
$conn = $db->getConnection();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

if ($action === 'view' && $id) {
    // Просмотр одной книги
    $query = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    
    if (!$book) {
        header("Location: books.php");
        exit();
    }
} else {
    // Список всех книг с возможностью сортировки
    $sort = $_GET['sort'] ?? 'title';
    $order = $_GET['order'] ?? 'ASC';
    
    $valid_sorts = ['title', 'author', 'year', 'price'];
    $sort = in_array($sort, $valid_sorts) ? $sort : 'title';
    $order = $order === 'DESC' ? 'DESC' : 'ASC';
    
    $query = "SELECT * FROM books ORDER BY $sort $order";
    $result = $conn->query($query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore - <?= $action === 'view' ? htmlspecialchars($book['title']) : 'Books' ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <?php if ($action === 'view'): ?>
            <section class="book-details">
                <div class="book-image">
                    <img src="assets/images/<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                </div>
                <div class="book-info">
                    <h1><?= htmlspecialchars($book['title']) ?></h1>
                    <p class="author">by <?= htmlspecialchars($book['author']) ?></p>
                    <p class="year">Published: <?= htmlspecialchars($book['year']) ?></p>
                    <p class="category">Category: <?= htmlspecialchars($book['category']) ?></p>
                    <p class="price">Price: $<?= number_format($book['price'], 2) ?></p>
                    <p class="stock">Availability: <?= $book['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?></p>
                    
                    <div class="rental-options">
                        <h3>Rental Options:</h3>
                        <ul>
                            <li>2 Weeks: $<?= number_format($book['rental_price_week'], 2) ?></li>
                            <li>1 Month: $<?= number_format($book['rental_price_month'], 2) ?></li>
                            <li>3 Months: $<?= number_format($book['rental_price_quarter'], 2) ?></li>
                        </ul>
                    </div>
                    
                    <form action="cart.php" method="post">
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <select name="action">
                            <option value="purchase">Purchase</option>
                            <option value="rent_2weeks">Rent for 2 Weeks</option>
                            <option value="rent_month">Rent for 1 Month</option>
                            <option value="rent_3months">Rent for 3 Months</option>
                        </select>
                        <button type="submit" class="btn">Add to Cart</button>
                    </form>
                    
                    <div class="description">
                        <h3>Description</h3>
                        <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <section class="books-list">
                <div class="sort-options">
                    <h2>Our Book Collection</h2>
                    <form method="get" action="books.php">
                        <label for="sort">Sort by:</label>
                        <select name="sort" id="sort">
                            <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>Title</option>
                            <option value="author" <?= $sort === 'author' ? 'selected' : '' ?>>Author</option>
                            <option value="year" <?= $sort === 'year' ? 'selected' : '' ?>>Year</option>
                            <option value="price" <?= $sort === 'price' ? 'selected' : '' ?>>Price</option>
                        </select>
                        
                        <label for="order">Order:</label>
                        <select name="order" id="order">
                            <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                            <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Descending</option>
                        </select>
                        
                        <button type="submit" class="btn">Apply</button>
                    </form>
                </div>
                
                <div class="books-grid">
                    <?php while($book = $result->fetch_assoc()): ?>
                    <div class="book-card">
                        <img src="assets/images/<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                        <h3><?= htmlspecialchars($book['title']) ?></h3>
                        <p><?= htmlspecialchars($book['author']) ?></p>
                        <p>$<?= number_format($book['price'], 2) ?></p>
                        <a href="books.php?action=view&id=<?= $book['id'] ?>" class="btn">View Details</a>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/scripts.js"></script>
</body>
</html>