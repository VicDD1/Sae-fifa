<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FIFA</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="antialiased">

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
    </header>

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; text-align: center;">
            {{ session('success') }}
        </div>
    @endif


    <!-- BANNIÈRE COOKIES + MODAL (inchangé) -->
    <div id="cookieBanner" class="cookie-banner" role="region" aria-label="Bannière cookies">
        <div class="cookie-banner__logo">BF</div>

        <div class="cookie-banner__text">
        <strong>Nous utilisons des cookies</strong><br>
        Nous et nos partenaires utilisons des traceurs pour personnaliser le contenu, mesurer les performances et vous proposer des publicités personnalisées.
        <a id="openPrefsLink" class="cookie-banner__link" href="#" role="button">Gérer mes préférences</a>
        </div>

        <div class="cookie-banner__actions">
        <button id="rejectAllBtn" class="btn btn-ghost">Refuser</button>
        <button id="acceptAllBtn" class="btn btn-primary">Accepter</button>
        </div>
    </div>

    <div id="overlay" class="overlay" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="prefs">
        <h2>Préférences des cookies</h2>
        <p>Choisissez les types de cookies que vous acceptez. Vous pouvez modifier ce choix à tout moment.</p>

        <div class="prefs__row">
            <div class="prefs__desc">
            <strong>Cookies nécessaires</strong>
            <div class="small">Indispensables au fonctionnement du site.</div>
            </div>
            <div class="prefs__toggle small">Toujours activés</div>
        </div>

        <div class="prefs__row">
            <div class="prefs__desc">
            <strong>Statistiques</strong>
            <div class="small">Permettent d'améliorer l'expérience utilisateur.</div>
            </div>
            <button class="toggle" data-key="analytics"><span class="knob"></span></button>
        </div>

        <div class="prefs__row">
            <div class="prefs__desc">
            <strong>Marketing</strong>
            <div class="small">Publicités personnalisées.</div>
            </div>
            <button class="toggle" data-key="marketing"><span class="knob"></span></button>
        </div>

        <div class="prefs__footer">
            <button id="savePrefsBtn" class="btn btn-primary">Enregistrer</button>
            <button id="closePrefsBtn" class="btn btn-ghost">Annuler</button>
        </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    

    @include('botman')
</body>
</html>
