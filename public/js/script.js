const STORAGE_KEY = "cookieConsent";

/**
 * Analyse les cookies réels du navigateur
 */
function updateDynamicCookieList() {
    const tbody = document.getElementById('dynamic-cookie-list');
    if (!tbody) return;

    const cookies = document.cookie.split(';');
    tbody.innerHTML = '';

    if (cookies[0].trim() === "") {
        tbody.innerHTML = '<tr><td colspan="3">Aucun cookie détecté.</td></tr>';
        return;
    }

    cookies.forEach(cookie => {
        const [name] = cookie.split('=');
        const row = document.createElement('tr');
        
        let usage = "Cookie technique";
        if (name.includes('_ga')) usage = "Statistiques (Google)";
        if (name.includes('_fbp')) usage = "Marketing (Facebook)";
        if (name.includes('XSRF')) usage = "Sécurité (Protection)";
        if (name.includes('site_decoration_preference')) usage = "Décoration (Inutile/Démo)";

        row.innerHTML = `
            <td><strong>${name.trim()}</strong></td>
            <td>Session</td>
            <td>${usage}</td>
        `;
        tbody.appendChild(row);
    });
}
function toggleFakeCookie(shouldExist) {
    if (shouldExist) {
        document.cookie = "site_decoration_preference=biscuits_au_chocolat; path=/; max-age=3600";
    } else {
        document.cookie = "site_decoration_preference=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
}
let isDecoEnabled = false;
/* --- Fonctions de gestion du bandeau --- */
function confirmConsent(isAccepted) {
    const finalChoice = isAccepted === true ? true : isDecoEnabled;
    localStorage.setItem(STORAGE_KEY, JSON.stringify({
        accepted: isAccepted,
        deco: finalChoice,
        date: new Date().toISOString()
    }));
    toggleFakeCookie(finalChoice);

    document.getElementById("cookieBanner").style.display = "none";
    const overlay = document.getElementById("overlay");
    if (overlay) overlay.classList.remove("open");

    // Mise à jour visuelle immédiate du tableau
    updateDynamicCookieList();
}

/* --- Initialisation et Événements --- */
// On utilise DOMContentLoaded pour que ça s'exécute le plus vite possible
document.addEventListener("DOMContentLoaded", () => {
    const fakeToggle = document.getElementById("fakeCookieToggle");
    
    if (fakeToggle) {
        fakeToggle.onclick = () => {
            isDecoEnabled = !isDecoEnabled;
            fakeToggle.classList.toggle("on", isDecoEnabled);
        };
    }
    const banner = document.getElementById("cookieBanner");
    const overlay = document.getElementById("overlay");

    // MODIFICATION ICI : On vérifie TOUT DE SUITE si on doit cacher le bandeau
    if (localStorage.getItem(STORAGE_KEY)) {
        if (banner) banner.style.display = "none";
    } else {
        if (banner) banner.style.display = "block";
    }

    // Gestion des clics (avec sécurité si les boutons n'existent pas sur toutes les pages)
    document.getElementById("openPrefsLink").onclick = (e) => {
        e.preventDefault();
        updateDynamicCookieList();
        overlay.classList.add("open");
    };

    document.getElementById("savePrefsBtn").onclick = () => confirmConsent(true);
    document.getElementById("acceptAllBtn").onclick = () => confirmConsent(true);
    document.getElementById("rejectAllBtn").onclick = () => confirmConsent(false);
    
    // Bouton annuler de la modal
    document.getElementById("closePrefsBtn").onclick = () => {
        overlay.classList.remove("open");
    };
    updateDynamicCookieList();
});