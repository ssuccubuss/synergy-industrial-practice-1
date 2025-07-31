<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
redirectIfNotLoggedIn();

$db = new Database();
$conn = $db->getConnection();

// Обработка действий с корзиной
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        unset($_SESSION['cart'][$_POST['book_id']]);
    }
}

// Получение информации о книгах в корзине
$cart_books = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $query = "SELECT * FROM books WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($book = $result->fetch_assoc()) {
        $cart_item = $_SESSION['cart'][$book['id']];
        $book['action'] = $cart_item['action'];
        
        if (strpos($cart_item['action'], 'rent') === 0) {
            $book['price'] = match($cart_item['action']) {
                'rent_2weeks' => $book['rental_price_week'],
                'rent_month' => $book['rental_price_month'],
                'rent_3months' => $book['rental_price_quarter'],
                default => 0
            };
        }
        
        $total += $book['price'];
        $cart_books[] = $book;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - BookStore</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="cart-container">
        <h1>Your Shopping Cart</h1>
        
        <?php if (empty($cart_books)): ?>
            <div class="empty-cart">
                <p>Your cart is currently empty.</p>
                <a href="books.php" class="btn">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <table>
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Action</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_books as $book): ?>
                        <tr>
                            <td>
                                <img src="assets/images/<?= htmlspecialchars($book['image']) ?>" 
                                     alt="<?= htmlspecialchars($book['title']) ?>" 
                                     class="cart-book-image">
                            </td>
                            <td><?= htmlspecialchars($book['title']) ?></td>
                            <td><?= htmlspecialchars($book['author']) ?></td>
                            <td>
                                <?= match($book['action']) {
                                    'purchase' => 'Purchase',
                                    'rent_2weeks' => 'Rent (2 weeks)',
                                    'rent_month' => 'Rent (1 month)',
                                    'rent_3months' => 'Rent (3 months)'
                                } ?>
                            </td>
                            <td>$<?= number_format($book['price'], 2) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                    <button type="submit" name="remove" class="btn btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right"><strong>Total:</strong></td>
                            <td>$<?= number_format($total, 2) ?></td>
                            <td>
                                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/scripts.js"></script>
</body>
</html>