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

// Динамическая загрузка постов по тегам
document.querySelectorAll('.tag-cloud .tag').forEach(tag => {
    tag.addEventListener('click', async function(e) {
        if (!this.classList.contains('active')) {
            e.preventDefault();
            const tagId = this.getAttribute('href').split('=')[1];
            const response = await fetch(`api/posts_by_tag.php?tag_id=${tagId}`);
            const posts = await response.json();
            // Обновите список постов на странице
        }
    });
});