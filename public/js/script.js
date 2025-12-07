const STORAGE_KEY = "cookieConsent";
const defaultPrefs = {
  necessary: true,
  analytics: false,
  marketing: false,
  timestamp: null
};

function getPrefs(){
  const raw = localStorage.getItem(STORAGE_KEY);
  return raw ? JSON.parse(raw) : null;
}

function savePrefs(p){
  p.timestamp = new Date().toISOString();
  localStorage.setItem(STORAGE_KEY, JSON.stringify(p));
}

/* --- Sélection des éléments --- */
const banner = document.getElementById("cookieBanner");
const overlay = document.getElementById("overlay");
const openPrefsLink = document.getElementById("openPrefsLink");
const acceptAllBtn = document.getElementById("acceptAllBtn");
const rejectAllBtn = document.getElementById("rejectAllBtn");
const savePrefsBtn = document.getElementById("savePrefsBtn");
const closePrefsBtn = document.getElementById("closePrefsBtn");
const toggles = document.querySelectorAll(".toggle");

/* --- Affichage / Masquage --- */
function hideBanner(){ banner.style.display = "none"; }
function showBanner(){ banner.style.display = "flex"; }

function setToggle(btn, state){
  btn.classList.toggle("on", state);
  btn.setAttribute("aria-pressed", state);
}

/* --- Ouvrir / Fermer les préférences --- */
openPrefsLink.addEventListener("click", e => {
  e.preventDefault();
  overlay.classList.add("open");
});

closePrefsBtn.addEventListener("click", ()=> {
  overlay.classList.remove("open");
});

/* --- Gestion des toggles --- */
toggles.forEach(t => {
  t.addEventListener("click", () => {
    const current = t.classList.contains("on");
    setToggle(t, !current);
  });
});

/* --- ACTIONS PRINCIPALES --- */
acceptAllBtn.addEventListener("click", () => {
  const prefs = { ...defaultPrefs, analytics:true, marketing:true };
  savePrefs(prefs);
  hideBanner();
});

rejectAllBtn.addEventListener("click", () => {
  const prefs = { ...defaultPrefs };
  savePrefs(prefs);
  hideBanner();
});

savePrefsBtn.addEventListener("click", () => {
  const prefs = { ...defaultPrefs };
  toggles.forEach(t => {
    prefs[t.dataset.key] = t.classList.contains("on");
  });
  savePrefs(prefs);
  overlay.classList.remove("open");
  hideBanner();
});

/* --- INIT: Vérifier si l'utilisateur a déjà choisi --- */
(function init(){
  const prefs = getPrefs();
  if(!prefs){
    showBanner();
  } else {
    hideBanner();
  }

  // Mettre à jour les toggles si des prefs existent
  const p = prefs || defaultPrefs;
  toggles.forEach(t => {
    const key = t.dataset.key;
    setToggle(t, p[key]);
  });
})();