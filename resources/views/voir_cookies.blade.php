<link rel="stylesheet" href="{{ asset('css/cookies.css') }}">

<header>
        <nav>
            <a href="/">Accueil</a>
            <a href="/produits">Fifa Store</a>


            <!-- CORRECTION : lien Vote propre -->
            <a href="{{ route('vote.page') }}">Vote</a>

            <a href="/players">Les joueurs</a>
            <a href="https://www.fifa.com/fr/news" target="_blank">Les Articles</a>

            @auth
                @php
                    $panier = \App\Models\Panier::where('id_user_connecte', Auth::id())->first();
                    $totalQuantite = $panier ? $panier->lignes->sum('quantitee') : 0;
                @endphp
            @endauth

            @guest
                @php $totalQuantite = 0; @endphp
            @endguest

            <a href="{{ route('panier.index') }}" style="margin-left: 15px; font-weight: bold;">
                <i class="fa-solid fa-cart-shopping"></i> Mon Panier ({{ $totalQuantite }})
            </a>

            @auth
                <div style="display: inline-flex; align-items: center; margin-left: 20px; color: white;">
                    
                    <a href="/mon-profil" style="text-decoration: none; display: flex; align-items: center;">
                        <span style="margin-right: 10px; font-weight: bold; border-bottom: 2px solid #00ff87;">
                            {{ Auth::user()->prenom_user_connecte ?? Auth::user()->surnom_user_connecte }}
                        </span>
                    </a>

                    <form action="/logout" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" title="Se déconnecter" style="background: none; border: none; cursor: pointer; color: #ffcccc;">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </form>

                </div>
            @endauth

            @guest
                <a href="/connexion" class="account_creation" title="Se connecter">
                    <img src="{{ asset('assets/icone.png') }}" alt="Compte">
                </a>
            @endguest
@auth

            @if (Auth::user()->id_user_connecte === 12 || Auth::user()->id_user_connecte === 11)
                <a class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>


                <a href="/proposer_un_produit"  class="account_creation"><p>faire une demande de produit</p></a>
                
            @endauth
            @auth
                <a href="{{ route('commande.liste') }}" class="btn btn-primary">
                    Mes commandes
                </a>
            @endauth

            @auth
                @if (!Auth::user()->professionnel)
                    <a href="/creer_un_compte_professionnel_1" class="account_creation">
                        <p>Compte professionnel</p>
                    </a>
                @endif

                @if (Auth::user()->professionnel)
                    <a href="/proposer_un_produit" class="account_creation">
                        <p>faire une demande de produit</p>
                    </a>
                @endif
            @endauth
        </nav>
        @endif
    </header>



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

<script src="{{ asset('js/script.js') }}"></script>

<script>
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
</script>