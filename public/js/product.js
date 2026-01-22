function toggleHelpModal() {
    const modal = document.getElementById('helpModal');
    if (modal.style.display === 'none' || modal.style.display === '') {
        modal.style.display = 'flex'; 
    } else {
        modal.style.display = 'none';
    }
}


window.onclick = function(event) {
    const modal = document.getElementById('helpModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}