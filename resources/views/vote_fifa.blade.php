<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vote FIFA</title>
    <link rel="stylesheet" href="{{ asset('css/account_vote_fifa.css') }}">
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
            @endif
                
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

<div id="vote_div">
<div class="card">

    <h1>Vote FIFA</h1>
    <p class="description">Veuillez sélectionner un thème, les joueurs et leur classement.</p>

    {{-- Message erreur "tu as déjà voté" --}}
    @if(session('erreur_vote'))
    <div class="error-message">
        {{ session('erreur_vote') }}
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

    <form method="POST" action="{{ route('vote.submit') }}">
        @csrf

        {{-- THÈME --}}
        <div class="theme-group">
            <label for="theme">Thème</label>
            <select name="theme" id="theme">
                <option value="">-- Sélectionnez un thème --</option>
                @foreach($themes as $theme)
                    <option value="{{ $theme->id_theme }}"
                        {{ old('theme') == $theme->id_theme ? 'selected' : '' }}>
                        {{ $theme->nom_theme }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- JOUEURS + CLASSEMENT --}}
        <div class="row">

            <div class="col-md-3">
                <label for="joueur1">Joueur 1</label>
                <select name="joueur1" id="joueur1">
                    <option value="">-- Sélectionnez un joueur --</option>
                    @foreach($joueurs as $joueur)
                        <option value="{{ $joueur->id_joueur }}"
                            {{ old('joueur1') == $joueur->id_joueur ? 'selected' : '' }}>
                            {{ $joueur->nom }}
                        </option>
                    @endforeach
                </select>

                <label class="classement-label">Classement Joueur 1</label>
                <select name="classement1" class="classement-select">
                    <option value="">-- Sélectionnez un classement --</option>
                    <option value="1" {{ old('classement1') == 1 ? 'selected' : '' }}>1er</option>
                    <option value="2" {{ old('classement1') == 2 ? 'selected' : '' }}>2ème</option>
                    <option value="3" {{ old('classement1') == 3 ? 'selected' : '' }}>3ème</option>
                    <option value="4" {{ old('classement1') == 4 ? 'selected' : '' }}>4ème</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="joueur2">Joueur 2</label>
                <select name="joueur2" id="joueur2">
                    <option value="">-- Sélectionnez un joueur --</option>
                    @foreach($joueurs as $joueur)
                        <option value="{{ $joueur->id_joueur }}"
                            {{ old('joueur2') == $joueur->id_joueur ? 'selected' : '' }}>
                            {{ $joueur->nom }}
                        </option>
                    @endforeach
                </select>

                <label class="classement-label">Classement Joueur 2</label>
                <select name="classement2" class="classement-select">
                    <option value="">-- Sélectionnez un classement --</option>
                    <option value="1" {{ old('classement2') == 1 ? 'selected' : '' }}>1er</option>
                    <option value="2" {{ old('classement2') == 2 ? 'selected' : '' }}>2ème</option>
                    <option value="3" {{ old('classement2') == 3 ? 'selected' : '' }}>3ème</option>
                    <option value="4" {{ old('classement2') == 4 ? 'selected' : '' }}>4ème</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="joueur3">Joueur 3</label>
                <select name="joueur3" id="joueur3">
                    <option value="">-- Sélectionnez un joueur --</option>
                    @foreach($joueurs as $joueur)
                        <option value="{{ $joueur->id_joueur }}"
                            {{ old('joueur3') == $joueur->id_joueur ? 'selected' : '' }}>
                            {{ $joueur->nom }}
                        </option>
                    @endforeach
                </select>

                <label class="classement-label">Classement Joueur 3</label>
                <select name="classement3" class="classement-select">
                    <option value="">-- Sélectionnez un classement --</option>
                    <option value="1" {{ old('classement3') == 1 ? 'selected' : '' }}>1er</option>
                    <option value="2" {{ old('classement3') == 2 ? 'selected' : '' }}>2ème</option>
                    <option value="3" {{ old('classement3') == 3 ? 'selected' : '' }}>3ème</option>
                    <option value="4" {{ old('classement3') == 4 ? 'selected' : '' }}>4ème</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="joueur4">Joueur 4</label>
                <select name="joueur4" id="joueur4">
                    <option value="">-- Sélectionnez un joueur --</option>
                    @foreach($joueurs as $joueur)
                        <option value="{{ $joueur->id_joueur }}"
                            {{ old('joueur4') == $joueur->id_joueur ? 'selected' : '' }}>
                            {{ $joueur->nom }}
                        </option>
                    @endforeach
                </select>

                <label class="classement-label">Classement Joueur 4</label>
                <select name="classement4" class="classement-select">
                    <option value="">-- Sélectionnez un classement --</option>
                    <option value="1" {{ old('classement4') == 1 ? 'selected' : '' }}>1er</option>
                    <option value="2" {{ old('classement4') == 2 ? 'selected' : '' }}>2ème</option>
                    <option value="3" {{ old('classement4') == 3 ? 'selected' : '' }}>3ème</option>
                    <option value="4" {{ old('classement4') == 4 ? 'selected' : '' }}>4ème</option>
                </select>
            </div>

        </div>

        {{-- BOUTONS --}}
        <div class="actions">
            <a href="{{ url('/') }}" class="btn-cancel">Retour</a>
            <button type="submit" class="btn-send">Valider</button>
        </div>

    </form>

</div>
</div>
<script src="{{ asset('js/vote.js') }}"></script>

    @include('botman')
</body>
</html>
