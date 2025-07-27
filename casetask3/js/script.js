document.addEventListener('DOMContentLoaded', function() {
    // Можно добавить интерактивность, например:
    // - AJAX для комментариев
    // - Динамическую загрузку постов
    // - Валидацию форм
    
    console.log('Блог загружен!');
    
    // Пример: подтверждение перед удалением
    const deleteForms = document.querySelectorAll('form[onsubmit]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Вы уверены, что хотите выполнить это действие?')) {
                e.preventDefault();
            }
        });
    });
});