<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | FIFA ID</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/account_creation.css') }}">
</head>
<body>
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
        <div class="left-panel">
            <div class="fifa-logo">FIFA ID</div>
            <div class="hero-text">
                <h1>Mon Espace Personnel</h1>
                <p>Retrouvez ici vos informations personnelles.</p>
            </div>
            <div style="margin-top: auto;">
                
            </div>
        </div>

        <div class="right-panel">
            <div class="login-box" style="max-width: 500px;">
                <h2 class="login-title">Mes Informations</h2>

                <form>
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Prénom</label>
                            <input type="text" class="custom-input" value="{{ $user->prenom_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Surnom</label>
                            <input type="text" class="custom-input" value="{{ $user->surnom_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">Email</label>
                        <input type="email" class="custom-input" value="{{ $user->courriel_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Date de naissance</label>
                            <input type="text" class="custom-input" value="{{ $user->date_de_naissance_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Pays</label>
                            <input type="text" class="custom-input" value="{{ $user->pays_de_naissance_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Langue</label>
                            <input type="text" class="custom-input" value="{{ $user->langue_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Équipe Favorite</label>
                            <input type="text" class="custom-input" value="{{ $user->favori_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">Mot de passe</label>
                        <div style="position: relative;">
                            <input type="password" class="custom-input" value="FakePassword123" readonly style="background-color: #f9f9f9; color: #555;">
                            <i class="fa-solid fa-lock password-icon"></i>
                        </div>
                    </div>

                    <a href="/parametre_compte" style="text-decoration: none;">
                        <button type="button" class="btn-login" style="background-color: #045694; cursor: pointer;">MODIFIER MES INFOS</button>
                    </a>
                </form>
            </div>
        </div>
    </div>
    @include('botman')
</body>
</html>