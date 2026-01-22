document.addEventListener('DOMContentLoaded', () => {
   
    if (typeof updateDynamicCookieList === "function") {
        updateDynamicCookieList();
    }

    
    const pageToggle = document.getElementById('pageCookieToggle');
    let isDecoActive = false;


    if (document.cookie.includes('site_decoration_preference')) {
        isDecoActive = true;
        pageToggle.classList.add('on');
    }


    if (pageToggle) {
        pageToggle.onclick = () => {
            isDecoActive = !isDecoActive;
            pageToggle.classList.toggle('on', isDecoActive);
        };
    }


    const saveBtn = document.getElementById('savePagePrefsBtn');
    if (saveBtn) {
        saveBtn.onclick = () => {
            
            localStorage.setItem("cookieConsent", JSON.stringify({
                accepted: true,
                deco: isDecoActive,
                date: new Date().toISOString()
            }));

           
            if (typeof toggleFakeCookie === "function") {
                toggleFakeCookie(isDecoActive);
            }

           
            updateDynamicCookieList();

            alert("Vos préférences ont été mises à jour !");
        };
    }

   
    const resetBtn = document.getElementById('resetConsentBtn');
    if (resetBtn) {
        resetBtn.onclick = () => {
            localStorage.removeItem('cookieConsent');
            
            if (typeof toggleFakeCookie === "function") toggleFakeCookie(false);
            
            alert('Préférences effacées. Le bandeau réapparaîtra.');
            window.location.href = '/'; 
        };
    }
});