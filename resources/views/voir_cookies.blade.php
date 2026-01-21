<link rel="stylesheet" href="{{ asset('css/cookies.css') }}">
<link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
@include('header')



    <div class="container">
    <h1 class="page-title">Gestion de vos préférences cookies</h1>
    <p class="page-intro">
        Gérez ici vos consentements. Les modifications sont appliquées immédiatement après enregistrement.
    </p>

    <div class="prefs">
        <div class="prefs__body">
            
            <div class="section-essential">
                <h3 class="section-title">1. Audit en temps réel</h3>
                <p class="section-desc">Traceurs actuellement détectés sur votre navigateur pour ce domaine.</p>

                <div class="cookie-list">
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Expiration</th>
                                <th>Nature</th>
                            </tr>
                        </thead>
                        <tbody id="dynamic-cookie-list">
                            <tr>
                                <td colspan="3" class="loading-text">Chargement de l'analyse...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <hr class="section-divider">

            <div class="section-optional">
                <h3 class="section-title">2. Personnalisation</h3>
                <p class="section-desc">Autorisez ou refusez les cookies non essentiels.</p>

                <div class="prefs__row_choice">
                    <div class="prefs__desc">
                        <strong>Cookie de décoration (Démo)</strong>
                        <div class="small">Active le cookie factice "biscuits au chocolat".</div>
                    </div>
                    <button type="button" id="pageCookieToggle" class="toggle">
                        <div class="knob"></div>
                    </button>
                </div>
            </div>

        </div>

        <div class="prefs__footer">
            <button id="savePagePrefsBtn" class="btn btn-primary">Enregistrer les modifications</button>
            <button id="resetConsentBtn" class="btn btn-ghost">Réinitialiser mes choix</button>
            <a href="/" class="back-link">&larr; Retour à l'accueil</a>
        </div>
    </div>
    
</div>

<script src="js/script.js"></script>

<script src="js/cookie.js"></script>
