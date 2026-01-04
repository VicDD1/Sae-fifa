<link rel="stylesheet" href="{{ asset('css/cookies.css') }}">

<div id="cookieBanner" class="cookie-banner" role="region" aria-label="Bannière cookies">
    <div class="cookie-banner__content">
        <div class="cookie-banner__logo">BF</div>
        <div class="cookie-banner__text">
            <strong>Gestion de votre confidentialité</strong>
            Ce site utilise des cookies techniques pour assurer votre sécurité et le bon fonctionnement de votre session.
            <button id="openPrefsLink" class="cookie-banner__link" type="button">Consulter les détails</button>
        </div>
        <div class="cookie-banner__actions">
            <button id="rejectAllBtn" class="btn btn-ghost" type="button">Refuser</button>
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
            <div class="prefs__row">
                <details class="prefs__details" open>
                    <summary>
                        <div class="prefs__desc">
                            <strong>Liste des cookies actifs</strong>
                            <div class="small">Les cookies nécessaires ne peuvent pas être désactivés pour garantir votre sécurité.</div>
                        </div>
                        <span class="badge">Analyse en direct</span>
                    </summary>
                    
                    <div class="cookie-list">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Expiration</th>
                                    <th>Usage</th>
                                </tr>
                            </thead>
                            <tbody id="dynamic-cookie-list">
                                <tr>
                                    <td colspan="3" style="text-align:center; padding:20px;">
                                        Chargement des cookies...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </details>
            </div>
        </div>

        <div class="prefs__footer">
            <button id="savePrefsBtn" class="btn btn-primary" type="button">Fermer et enregistrer</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/script.js') }}"></script>