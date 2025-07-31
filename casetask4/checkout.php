<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
redirectIfNotLoggedIn();

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Обработка оформления заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    
    try {
        foreach ($_SESSION['cart'] as $book_id => $item) {
            if ($item['action'] === 'purchase') {
                // Обработка покупки
                $query = "INSERT INTO purchases (user_id, book_id, price) 
                          VALUES (?, ?, (SELECT price FROM books WHERE id = ?))";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iii", $_SESSION['user_id'], $book_id, $book_id);
                $stmt->execute();
                
                // Уменьшение количества книг
                $query = "UPDATE books SET stock = stock - 1 WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $book_id);
                $stmt->execute();
            } else {
                // Обработка аренды
                $rental_type = match($item['action']) {
                    'rent_2weeks' => '2weeks',
                    'rent_month' => 'month',
                    'rent_3months' => '3months'
                };
                
                $start_date = date('Y-m-d');
                $end_date = match($rental_type) {
                    '2weeks' => date('Y-m-d', strtotime('+2 weeks')),
                    'month' => date('Y-m-d', strtotime('+1 month')),
                    '3months' => date('Y-m-d', strtotime('+3 months'))
                };
                
                $query = "INSERT INTO rentals (user_id, book_id, rental_type, start_date, end_date) 
                          VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iisss", $_SESSION['user_id'], $book_id, $rental_type, $start_date, $end_date);
                $stmt->execute();
            }
        }
        
        $conn->commit();
        unset($_SESSION['cart']);
        header("Location: profile.php?success=order_complete");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Order processing failed";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BookStore</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="checkout-container">
        <h1>Checkout</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="checkout-steps">
            <div class="step active">1. Review Order</div>
            <div class="step">2. Payment</div>
            <div class="step">3. Confirmation</div>
        </div>
        
        <div class="order-summary">
            <h2>Order Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Action</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $book_id => $item): 
                        $book = getBookById($book_id); // Предполагаем существование этой функции
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td>
                            <?= match($item['action']) {
                                'purchase' => 'Purchase',
                                'rent_2weeks' => 'Rent (2 weeks)',
                                'rent_month' => 'Rent (1 month)',
                                'rent_3months' => 'Rent (3 months)'
                            } ?>
                        </td>
                        <td>
                            $<?= match($item['action']) {
                                'purchase' => number_format($book['price'], 2),
                                'rent_2weeks' => number_format($book['rental_price_week'], 2),
                                'rent_month' => number_format($book['rental_price_month'], 2),
                                'rent_3months' => number_format($book['rental_price_quarter'], 2)
                            } ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2"><strong>Total:</strong></td>
                        <td><strong>$<?= number_format($total, 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="payment-methods">
            <h2>Payment Method</h2>
            <form method="post">
                <div class="form-group">
                    <input type="radio" name="payment_method" value="credit_card" id="credit_card" checked>
                    <label for="credit_card">Credit Card</label>
                </div>
                <div class="form-group">
                    <input type="radio" name="payment_method" value="paypal" id="paypal">
                    <label for="paypal">PayPal</label>
                </div>
                
                <div id="credit-card-details">
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiry">Expiry Date</label>
                            <input type="text" id="expiry" name="expiry" placeholder="MM/YY">
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="card_name">Name on Card</label>
                        <input type="text" id="card_name" name="card_name" placeholder="John Smith">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Complete Purchase</button>
            </form>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/scripts.js"></script>
    <script>
        // Переключение между методами оплаты
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('credit-card-details').style.display = 
                    this.value === 'credit_card' ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>