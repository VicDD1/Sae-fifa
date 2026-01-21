function toggleHelpModal() {
    const modal = document.getElementById('helpModal');
    if (modal.style.display === 'none' || modal.style.display === '') {
        modal.style.display = 'flex'; // Affiche en mode Flexbox pour centrer
    } else {
        modal.style.display = 'none';
    }
}

// Fermer si on clique en dehors de la boîte blanche
window.onclick = function(event) {
    const modal = document.getElementById('helpModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}