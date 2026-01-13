<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une Votation</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/account_vote_fifa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>
<body>
    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex; width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>
            <a href="{{ route('vote.page') }}">Vote</a>
            <a href="/players">Les joueurs</a>
            <a href="/blog">L'Actu</a>

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
                        <img style="text-decoration: none; display: flex; align-items: center; width:40px;" src="{{asset('assets/iconEdit.png')}}" alt="voir mes informations">
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
                @if (Auth::user()->id_user_connecte === 12 || Auth::user()->id_user_connecte === 11 || Auth::user()->id_user_connecte === 13)
                    <a style="margin-left: auto;" class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Statistiques"></a>
                    <a style="margin-left: auto;" class="account_creation" href="/localisation_des_ventes"><img src="{{ asset('assets/map.png') }}" alt="Carte"></a>
                    <a href="{{ route('theme_vote.create') }}" class="account_creation" style="color: #00ff87; font-weight: bold;">+ Votation</a>
                @endif
            @endauth
        </nav>
    </header>

    <div id="vote_div">
        <div class="card">
            <h1>Créer une nouvelle Votation</h1>
            <p class="description">En tant que membre du service vente, vous pouvez créer une nouvelle votation pour les visiteurs.</p>

            {{-- Message de succès --}}
            @if(session('success'))
                <div style="background-color: #00ff87; color: #1a1a2e; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Affichage des erreurs de validation --}}
            @if ($errors->any())
                <div style="color:#b00020; margin-bottom:20px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('theme_vote.store') }}">
                @csrf

                {{-- NOM DU THÈME --}}
                <div class="theme-group">
                    <label for="nom_theme">Nom du thème de votation</label>
                    <input type="text" 
                           name="nom_theme" 
                           id="nom_theme" 
                           value="{{ old('nom_theme') }}" 
                           placeholder="Ex: Joueur trop vieux"
                           style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px;"
                           required>
                </div>

                {{-- DATE DE FIN DE VOTE --}}
                <div class="theme-group" style="margin-top: 20px;">
                    <label for="date_fin_vote">Date de fin du vote</label>
                    <input type="date" 
                           name="date_fin_vote" 
                           id="date_fin_vote" 
                           value="{{ old('date_fin_vote') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px;"
                           required>
                </div>

                {{-- BOUTONS --}}
                <div class="actions" style="margin-top: 30px;">
                    <a href="{{ url('/') }}" class="btn-cancel">Retour</a>
                    <button type="submit" class="btn-send">Créer la votation</button>
                </div>
            </form>
        </div>
    </div>

    @include('botman')
</body>
</html>
