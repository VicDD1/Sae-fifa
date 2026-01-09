<link rel="stylesheet" href="{{ asset('css/cookies.css') }}">
<link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
<div id="cookieBanner" class="cookie-banner" role="region" aria-label="Bannière cookies">
    <div class="cookie-banner__content">
        <div class="cookie-banner__logo">BF</div>
        <div class="cookie-banner__text">
            <strong>Gestion de votre confidentialité</strong>
            Ce site utilise des cookies techniques pour assurer votre sécurité et le bon fonctionnement de votre session.
            <button id="openPrefsLink" class="cookie-banner__link" type="button">Consulter les détails</button>
        </div>
        <div class="cookie-banner__actions">
            <button id="rejectAllBtn" class="btn btn-primary" type="button">Refuser</button>
            <button id="acceptAllBtn" class="btn btn-primary" type="button">J'ai compris</button>
        </div>
    </div>
</div>

<div id="overlay" class="overlay" role="dialog" aria-modal="true">
    <div class="prefs">
        <div class="prefs__header">
            <h2>Cookies détectés sur votre navigateur</h2>
            <p>Voici la liste en temps réel des traceurs utilisés par ce site.</p>
        </div>

        <div class="prefs__body">
    
    <div class="section-essential">
        <h3 class="section-title">1. Cookies Techniques & Détectés</h3>
        <p class="section-desc">Ces cookies sont nécessaires au fonctionnement ou actuellement actifs sur votre session.</p>
        
        <div class="prefs__row">
            <details class="prefs__details" open>
                <summary>
                    <div class="prefs__desc">
                        <strong>Scanner en temps réel</strong>
                        <div class="small">État actuel de votre navigateur.</div>
                    </div>
                    <span class="badge">Analyse en direct</span>
                </summary>
                
                <div class="cookie-list">
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Exp.</th>
                                <th>Usage</th>
                            </tr>
                        </thead>
                        <tbody id="dynamic-cookie-list">
                            <tr>
                                <td colspan="3" style="text-align:center; padding:20px;">
                                    Chargement...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    </div>

    <hr class="section-divider">

    <div class="section-optional">
        <h3 class="section-title">2. Personnalisation</h3>
        <p class="section-desc">Gérez ici les cookies qui ne sont pas essentiels (Décoration, Marketing).</p>

        <div class="prefs__row_choice">
            <div class="prefs__desc">
                <strong>Cookie de décoration (Démo)</strong>
                <div class="small">"Biscuits au chocolat" (Non essentiel).</div>
            </div>
            <button type="button" id="fakeCookieToggle" class="toggle">
                <div class="knob"></div>
            </button>
        </div>
    </div>

</div>

        <div class="prefs__footer">
        <button type="button" onclick="confirmConsent(false)" class="btn btn-primary">
    Fermer et enregistrer
</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/script.js') }}"></script>