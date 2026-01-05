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

        row.innerHTML = `
            <td><strong>${name.trim()}</strong></td>
            <td>Session</td>
            <td>${usage}</td>
        `;
        tbody.appendChild(row);
    });
}

/* --- Fonctions de gestion du bandeau --- */
function confirmConsent() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify({
        accepted: true,
        date: new Date().toISOString()
    }));
    document.getElementById("cookieBanner").style.display = "none";
    document.getElementById("overlay").classList.remove("open");
}

/* --- Initialisation et Événements --- */
// On utilise DOMContentLoaded pour que ça s'exécute le plus vite possible
document.addEventListener("DOMContentLoaded", () => {
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

    document.getElementById("savePrefsBtn").onclick = () => confirmConsent();
    document.getElementById("acceptAllBtn").onclick = () => confirmConsent();
    document.getElementById("rejectAllBtn").onclick = () => confirmConsent();
    
    // Bouton annuler de la modal
    document.getElementById("closePrefsBtn").onclick = () => {
        overlay.classList.remove("open");
    };
});