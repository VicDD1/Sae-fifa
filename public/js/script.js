const STORAGE_KEY = "cookieConsent";
let isDecoEnabled = false; // Variable globale pour suivre l'état du bouton

/**
 * Analyse les cookies réels du navigateur (Audit)
 */
function updateDynamicCookieList() {
    const tbody = document.getElementById('dynamic-cookie-list');
    if (!tbody) return;

    const cookies = document.cookie.split(';');
    tbody.innerHTML = '';

    if (cookies[0].trim() === "") {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding:10px;">Aucun cookie détecté.</td></tr>';
        return;
    }

    cookies.forEach(cookie => {
        const parts = cookie.split('=');
        const name = parts[0].trim();
        const row = document.createElement('tr');
        
        let usage = "Cookie technique";
        if (name.includes('_ga')) usage = "Statistiques (Google)";
        if (name.includes('_fbp')) usage = "Marketing (Facebook)";
        if (name.includes('XSRF')) usage = "Sécurité (Protection)";
        if (name.includes('site_decoration_preference')) usage = "Décoration (Inutile/Démo)";

        row.innerHTML = `
            <td><strong>${name}</strong></td>
            <td>Session</td>
            <td>${usage}</td>
        `;
        tbody.appendChild(row);
    });
}

/**
 * Crée ou supprime réellement le cookie sur le navigateur
 */
function toggleFakeCookie(shouldExist) {
    if (shouldExist) {
        document.cookie = "site_decoration_preference=biscuits_au_chocolat; path=/; max-age=3600; SameSite=Lax";
    } else {
        document.cookie = "site_decoration_preference=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC; SameSite=Lax";
    }
}

/**
 * Enregistre le choix de l'utilisateur
 */
function confirmConsent(isAccepted) {
    // CORRECTION : On utilise 'isAccepted' (le paramètre de la fonction)
    // Si isAccepted est true (bouton Tout Accepter), on force true.
    // Sinon, on prend la valeur de l'interrupteur (isDecoEnabled).
    const finalDecoChoice = (isAccepted === true) ? true : isDecoEnabled;

    localStorage.setItem(STORAGE_KEY, JSON.stringify({
        accepted: isAccepted,
        deco: finalDecoChoice,
        date: new Date().toISOString()
    }));

    // Action sur le cookie réel
    toggleFakeCookie(finalDecoChoice);

    // Fermeture visuelle
    const banner = document.getElementById("cookieBanner");
    if (banner) banner.style.display = "none";
    
    const overlay = document.getElementById("overlay");
    if (overlay) overlay.classList.remove("open");

    // Rafraîchir l'affichage du tableau
    updateDynamicCookieList();
}

/**
 * Initialisation
 */
document.addEventListener("DOMContentLoaded", () => {
    // 1. Gestion du Toggle (Modal ou Page dédiée)
    const fakeToggle = document.getElementById("fakeCookieToggle") || document.getElementById("pageCookieToggle");
    
    if (fakeToggle) {
        // État initial selon le cookie existant
        isDecoEnabled = document.cookie.includes('site_decoration_preference');
        fakeToggle.classList.toggle("on", isDecoEnabled);

        fakeToggle.onclick = () => {
            isDecoEnabled = !isDecoEnabled;
            fakeToggle.classList.toggle("on", isDecoEnabled);
        };
    }

    // 2. Affichage automatique du bandeau
    const banner = document.getElementById("cookieBanner");
    if (!localStorage.getItem(STORAGE_KEY) && banner) {
        banner.style.display = "block";
    }

    // 3. Liaison des boutons (avec sécurité si les IDs n'existent pas sur la page)
    const bindBtn = (id, action) => {
        const btn = document.getElementById(id);
        if (btn) btn.onclick = action;
    };

    bindBtn("acceptAllBtn", () => confirmConsent(true));
    bindBtn("rejectAllBtn", () => confirmConsent(false));
    bindBtn("savePrefsBtn", () => confirmConsent(false)); // Utilise le toggle
    
    // Pour le bouton de la page de gestion spécifique
    bindBtn("savePagePrefsBtn", () => confirmConsent(false));

    bindBtn("openPrefsLink", (e) => {
        e.preventDefault();
        updateDynamicCookieList();
        const overlay = document.getElementById("overlay");
        if (overlay) overlay.classList.add("open");
    });

    bindBtn("closePrefsBtn", () => {
        const overlay = document.getElementById("overlay");
        if (overlay) overlay.classList.remove("open");
    });

    // Premier audit au chargement
    updateDynamicCookieList();
});