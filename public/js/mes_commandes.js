document.querySelectorAll('.toggle-commande').forEach(header => {
    header.addEventListener('click', () => {
        const card = header.closest('.commande-card');
        const details = card.querySelector('.commande-details');
        const icon = header.querySelector('.toggle-icon');

        const open = details.style.display === 'block';

        details.style.display = open ? 'none' : 'block';
        icon.textContent = open ? '+' : '−';
    });
});