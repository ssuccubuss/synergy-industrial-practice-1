<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

$db = new Database();
$conn = $db->getConnection();

// Обработка действий с книгами
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_book'])) {
        // Обработка добавления книги
    } elseif (isset($_POST['edit_book'])) {
        // Обработка редактирования книги
    } elseif (isset($_POST['delete_book'])) {
        // Обработка удаления книги
    }
}

// Получение списка книг
$query = "SELECT * FROM books ORDER BY title";
$books = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - BookStore Admin</title>
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
                        <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="books_management.php" class="active"><i class="fas fa-book"></i> Manage Books</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        
        <main class="admin-main">
            <div class="container">
                <div class="books-management">
                    <div class="admin-actions">
                        <h1><i class="fas fa-book"></i> Manage Books</h1>
                        <button id="add-book-btn" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Book
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cover</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?= $book['id'] ?></td>
                                    <td>
                                        <img src="../assets/images/<?= htmlspecialchars($book['image']) ?>" 
                                             alt="<?= htmlspecialchars($book['title']) ?>" 
                                             class="admin-book-cover">
                                    </td>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td>$<?= number_format($book['price'], 2) ?></td>
                                    <td>
                                        <span class="<?= $book['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= $book['stock'] ?>
                                        </span>
                                    </td>
                                    <td class="action-btns">
                                        <button class="btn btn-edit" onclick="openEditModal(<?= $book['id'] ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                            <button type="submit" name="delete_book" class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this book?')">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Модальное окно добавления книги -->
                    <div id="add-book-modal" class="admin-modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2><i class="fas fa-plus"></i> Add New Book</h2>
                                <button class="close-modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form method="post" enctype="multipart/form-data" class="admin-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" id="title" name="title" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="author">Author</label>
                                            <input type="text" id="author" name="author" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea id="description" name="description" rows="5" required></textarea>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <input type="text" id="category" name="category" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="year">Year</label>
                                            <input type="number" id="year" name="year" min="1000" max="<?= date('Y') ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <input type="number" id="price" name="price" min="0" step="0.01" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock">Stock</label>
                                            <input type="number" id="stock" name="stock" min="0" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="rent_week">2 Weeks Rental</label>
                                            <input type="number" id="rent_week" name="rent_week" min="0" step="0.01" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="rent_month">1 Month Rental</label>
                                            <input type="number" id="rent_month" name="rent_month" min="0" step="0.01" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="rent_quarter">3 Months Rental</label>
                                            <input type="number" id="rent_quarter" name="rent_quarter" min="0" step="0.01" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="image">Book Cover</label>
                                        <div class="image-preview" id="image-preview">
                                            <span>No image selected</span>
                                        </div>
                                        <input type="file" id="image" name="image" accept="image/*" required 
                                               onchange="previewImage(this, 'image-preview')">
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                                        <button type="submit" name="add_book" class="btn btn-primary">Add Book</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Модальное окно редактирования книги -->
                    <div id="edit-book-modal" class="admin-modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2><i class="fas fa-edit"></i> Edit Book</h2>
                                <button class="close-modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form method="post" enctype="multipart/form-data" class="admin-form">
                                    <input type="hidden" id="edit_id" name="book_id">
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="edit_title">Title</label>
                                            <input type="text" id="edit_title" name="title" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_author">Author</label>
                                            <input type="text" id="edit_author" name="author" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Остальные поля формы аналогично добавлению книги -->
                                    
                                    <div class="form-group">
                                        <label for="edit_image">Book Cover</label>
                                        <div class="image-preview" id="edit-image-preview">
                                            <span>Current image will be kept</span>
                                        </div>
                                        <input type="file" id="edit_image" name="image" accept="image/*"
                                               onchange="previewImage(this, 'edit-image-preview')">
                                        <small>Leave empty to keep current image</small>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                                        <button type="submit" name="edit_book" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/scripts.js"></script>
    <script>
        // Открытие модального окна добавления книги
        document.getElementById('add-book-btn').addEventListener('click', function() {
            document.getElementById('add-book-modal').classList.add('active');
        });
        
        // Функция для открытия модального окна редактирования
        function openEditModal(bookId) {
            // Здесь должен быть AJAX-запрос для получения данных книги
            fetch(`get_book_data.php?id=${bookId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_id').value = data.id;
                    document.getElementById('edit_title').value = data.title;
                    document.getElementById('edit_author').value = data.author;
                    // Заполните остальные поля...
                    
                    // Превью текущего изображения
                    const preview = document.getElementById('edit-image-preview');
                    preview.innerHTML = `<img src="../assets/images/${data.image}" alt="${data.title}">`;
                    
                    document.getElementById('edit-book-modal').classList.add('active');
                })
                .catch(error => console.error('Error:', error));
        }
        
        // Закрытие модальных окон
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', closeAllModals);
        });
        
        // Закрытие при клике вне модального окна
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('admin-modal')) {
                closeAllModals();
            }
        });
        
        function closeAllModals() {
            document.querySelectorAll('.admin-modal').forEach(modal => {
                modal.classList.remove('active');
            });
        }
        
        // Превью изображения
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            }
            
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>