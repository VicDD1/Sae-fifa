document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialisation de la liste (Audit)
    if (typeof updateDynamicCookieList === "function") {
        updateDynamicCookieList();
    }

    // 2. Gestion de l'interrupteur (Toggle) sur cette page
    const pageToggle = document.getElementById('pageCookieToggle');
    let isDecoActive = false;

    // VERIFICATION INITIALE : On regarde si le cookie existe vraiment
    // pour mettre l'interrupteur dans la bonne position au chargement de la page
    if (document.cookie.includes('site_decoration_preference')) {
        isDecoActive = true;
        pageToggle.classList.add('on');
    }

    // Interaction au clic
    if (pageToggle) {
        pageToggle.onclick = () => {
            isDecoActive = !isDecoActive;
            pageToggle.classList.toggle('on', isDecoActive);
        };
    }

    // 3. Bouton "Enregistrer les modifications"
    const saveBtn = document.getElementById('savePagePrefsBtn');
    if (saveBtn) {
        saveBtn.onclick = () => {
            // On met à jour le localStorage
            // Note : on garde "accepted: true" car l'utilisateur est en train de paramétrer
            localStorage.setItem("cookieConsent", JSON.stringify({
                accepted: true,
                deco: isDecoActive,
                date: new Date().toISOString()
            }));

            // On appelle ta fonction globale (dans script.js) pour créer/détruire le cookie
            if (typeof toggleFakeCookie === "function") {
                toggleFakeCookie(isDecoActive);
            }

            // On rafraîchit la liste visuelle pour montrer le changement immédiat
            updateDynamicCookieList();

            alert("Vos préférences ont été mises à jour !");
        };
    }

    // 4. Bouton "Tout réinitialiser" (Déjà existant)
    const resetBtn = document.getElementById('resetConsentBtn');
    if (resetBtn) {
        resetBtn.onclick = () => {
            localStorage.removeItem('cookieConsent');
            // On force la suppression du cookie déco aussi
            if (typeof toggleFakeCookie === "function") toggleFakeCookie(false);
            
            alert('Préférences effacées. Le bandeau réapparaîtra.');
            window.location.href = '/'; 
        };
    }
});