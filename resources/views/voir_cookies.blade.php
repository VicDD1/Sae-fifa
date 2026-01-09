<link rel="stylesheet" href="{{ asset('css/cookies.css') }}">
<link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex;  width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>


            <!-- CORRECTION : lien Vote propre -->
            <a href="{{ route('vote.page') }}">Vote</a>

            
            <a href="https://www.fifa.com/fr/news" target="_blank">L'Actu </a>

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
                        <img style="text-decoration: none; display: flex; align-items: center; width:40px;" src="{{asset('assets/iconEdit.png')}}" alt="voir mes informations"></img>
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
            <div class="nav-right-group">
                <a style="margin-left: auto;" class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>
                 <a style="margin-left: auto;" class="account_creation" href="/localisation_des_ventes"><img src="{{ asset('assets/map.png') }}" alt="Compte"></a>

            </div>
            @endif

                
            @endauth
            @auth
                @if (!Auth::user()->professionnel && Auth::user()->id_user_connecte !== 11 && Auth::user()->id_user_connecte !== 13)
                <a href="{{ route('commande.liste') }}" class="btn btn-primary">
                    Mes commandes
                </a>
                @endif
            @endauth

            @auth
                @if (!Auth::user()->professionnel && Auth::user()->id_user_connecte !== 11 && Auth::user()->id_user_connecte !== 13)
                    <a href="/creer_un_compte_professionnel_1" class="account_creation">
                        <p>Compte professionnel</p>
                    </a>
                @endif

                @if ((Auth::user()->id_user_connecte !== 12 || Auth::user()->id_user_connecte !== 11) && Auth::user()->professionnel)
                    <a href="/proposer_un_produit" class="account_creation">
                        <p>faire une demande de produit</p>
                    </a>
                @endif
            @endauth
        </nav>
    </header>



<div class="container" style="max-width: 800px; margin: 50px auto; padding: 20px; font-family: sans-serif;">
    <h1 style="color: #0b2640;">Gestion de vos préférences cookies</h1>
    <p style="color: #5b6e78; line-height: 1.6;">
        Vous pouvez à tout moment modifier vos choix concernant les traceurs utilisés sur ce site. 
        Actuellement, seuls les cookies nécessaires au fonctionnement sont actifs.
    </p>

    <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

    <div class="prefs" style="width: 100%; box-shadow: none; border: 1px solid #eee;">
        <div class="prefs__body">
            <div class="prefs__row">
                <details class="prefs__details" open>
                    <summary>
                        <div>
                            <strong>Cookies actuellement présents</strong>
                            <div class="small">Voici la liste des cookies détectés sur votre navigateur pour ce domaine.</div>
                        </div>
                        <span class="badge">Analyse dynamique</span>
                    </summary>
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
                                </tbody>
                        </table>
                    </div>
                </details>
            </div>
        </div>

        <div class="prefs__footer" style="margin-top: 30px; display: flex; gap: 15px; justify-content: flex-start;">
            <button id="resetConsentBtn" class="btn btn-primary">Réinitialiser mes choix</button>
            <a href="/" class="btn btn-ghost-dark" style="text-decoration: none; display: flex; align-items: center;">Retour à l'accueil</a>
        </div>
    </div>
</div>

<script src="{{ asset('js/script.js') }}"></script>

<script>
    // Petit script supplémentaire spécifique à cette page
    document.addEventListener('DOMContentLoaded', () => {
        // Force l'affichage de la liste au chargement
        if (typeof updateDynamicCookieList === "function") {
            updateDynamicCookieList();
        }

        // Bouton pour réinitialiser
        const resetBtn = document.getElementById('resetConsentBtn');
        if (resetBtn) {
            resetBtn.onclick = () => {
                localStorage.removeItem('cookieConsent');
                alert('Vos préférences ont été réinitialisées. Le bandeau s\'affichera au prochain chargement.');
                window.location.href = '/'; // Redirection vers l'accueil
            };
        }
    });
</script>