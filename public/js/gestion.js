document.querySelectorAll('select[name="statut_livraison"]').forEach(select => {
    select.addEventListener('change', function() {
        // On trouve le champ texte qui est juste à côté (le voisin)
        const inputMotif = this.nextElementSibling; 
        
        if (this.value === 'Refusé' || this.value === 'Réserve') {
            inputMotif.style.border = "2px solid red";
            inputMotif.placeholder = "MOTIF OBLIGATOIRE !";
            inputMotif.required = true;
        } else {
            inputMotif.style.border = "1px solid #ccc";
            inputMotif.placeholder = "Motif (si besoin)";
            inputMotif.required = false;
        }
    });
});