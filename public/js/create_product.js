function updateDropdownLabel(checkboxClass, buttonId, defaultText) {
    const checkboxes = document.querySelectorAll('.' + checkboxClass);
    const button = document.getElementById(buttonId);
    
    function update() {
        let selected = [];
        checkboxes.forEach(cb => {
            if (cb.checked) {
                // On récupère le texte du label associé
                selected.push(cb.nextElementSibling.innerText.trim());
            }
        });
        
        if (selected.length > 0) {
            // Affiche les 3 premiers éléments, puis "..." si plus
            if(selected.length > 3) {
                button.innerText = selected.slice(0, 3).join(', ') + ' et ' + (selected.length - 3) + ' autres';
            } else {
                button.innerText = selected.join(', ');
            }
        } else {
            button.innerText = defaultText;
        }
    }

    // Écouter les changements
    checkboxes.forEach(cb => cb.addEventListener('change', update));
    // Lancer une fois au chargement (pour les "old" inputs)
    update();
}

// Activer la fonction pour Tailles et Couleurs
document.addEventListener('DOMContentLoaded', function() {
    updateDropdownLabel('checkbox-taille', 'dropdownTailles', '-- Choisir les tailles --');
    updateDropdownLabel('checkbox-couleur', 'dropdownCouleurs', '-- Choisir les couleurs --');
});