// Fichier: public/js/blog_show.js

// Gestion ouverture/fermeture réponse
function toggleReply(id) {
    var element = document.getElementById(id);
    if (element.style.display === "none") {
        element.style.display = "block";
    } else {
        element.style.display = "none";
    }
}

// --- GESTION DE LA MODALE ---
let formIdToDelete = null;

function openDeleteModal(formId) {
    formIdToDelete = formId;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    formIdToDelete = null;
    document.getElementById('deleteModal').style.display = 'none';
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (formIdToDelete) {
        document.getElementById(formIdToDelete).submit();
    }
});

// Fermer si clic en dehors
window.onclick = function(event) {
    let modal = document.getElementById('deleteModal');
    if (event.target == modal) {
        closeDeleteModal();
    }
}