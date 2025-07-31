// Основные скрипты
document.addEventListener('DOMContentLoaded', function() {
    // Обработка модальных окон
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('close')) {
                this.style.display = 'none';
            }
        });
    });
    
    // Обработка форм
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing...';
            }
        });
    });
    
    // Анимации
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate');
        elements.forEach(el => {
            if (isElementInViewport(el)) {
                el.classList.add('animated');
            }
        });
    };
    
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll();
});

function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.bottom >= 0 &&
        rect.left <= (window.innerWidth || document.documentElement.clientWidth) &&
        rect.right >= 0
    );
}

// Функции для работы с корзиной
function addToCart(bookId, action) {
    fetch('cart_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({book_id: bookId, action: action})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            showNotification('Book added to cart');
        } else {
            showNotification(data.error, 'error');
        }
    });
}

function updateCartCount(count) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'block' : 'none';
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}

 function checkPasswordStrength(password) {
            const strengthMeter = document.getElementById('strength-meter');
            let strength = 0;
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
            
            const colors = ['#e74c3c', '#f39c12', '#f1c40f', '#2ecc71', '#27ae60'];
            const width = (strength / 5) * 100;
            
            strengthMeter.style.width = width + '%';
            strengthMeter.style.backgroundColor = colors[strength - 1] || '#eee';
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const message = document.getElementById('password-match-message');
            const registerBtn = document.getElementById('register-btn');
            
            if (password && confirmPassword) {
                if (password === confirmPassword) {
                    message.textContent = 'Passwords match!';
                    message.style.color = '#2ecc71';
                    registerBtn.disabled = false;
                } else {
                    message.textContent = 'Passwords do not match!';
                    message.style.color = '#e74c3c';
                    registerBtn.disabled = true;
                }
            } else {
                message.textContent = '';
                registerBtn.disabled = false;
            }
        }
        
        // Валидация формы перед отправкой
        document.getElementById('register-form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });