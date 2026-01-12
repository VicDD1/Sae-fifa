<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vote FIFA</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/account_vote_fifa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>
<body>
    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex;  width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>


            <!-- CORRECTION : lien Vote propre -->
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
                <a style="margin-left: auto;" class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>
                 <a style="margin-left: auto;" class="account_creation" href="/localisation_des_ventes"><img src="{{ asset('assets/map.png') }}" alt="Compte"></a>

            </div>
            @endif
            @if (Auth::user()->id_user_connecte === 35)
                <a class="account_creation" href="/gestion-rgpd">Espace DPO </a>
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


    <button onclick="toggleHelpModal()" style="position: fixed; bottom: 20px; left: 20px; background-color: #2563eb; color: white; width: 60px; height: 60px; border-radius: 50%; border: none; font-size: 30px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 99999; display: flex; align-items: center; justify-content: center;">
    ?
</button>

{{-- Fenêtre Modale d'Aide --}}
<div id="helpModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
    
    <div style="background: white; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto; border-radius: 12px; padding: 0; box-shadow: 0 20px 25px rgba(0,0,0,0.2); position: relative;">
        
        <div style="background: #2563eb; color: white; padding: 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-circle-question"></i> Guide du FIFA Store</h2>
            <button onclick="toggleHelpModal()" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer;">&times;</button>
        </div>

        <div style="padding: 25px;">
            
            <div style="margin-bottom: 25px;">
                <h3 style="color: #1e40af; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px; margin-top: 0; display: flex; align-items: center;">
                    <i class="fa-solid fa-magnifying-glass" style="margin-right: 10px;"></i> Trouver son maillot
                </h3>
                <ul style="font-size: 14px; color: #4b5563; padding-left: 20px; line-height: 1.6;">
                    <li><strong>Filtres :</strong> Utilisez le menu de gauche pour trier par Nation, Catégorie ou Budget.</li>
                    <li><strong>Astuce :</strong> Sélectionnez d'abord une <em>Catégorie</em> et appliquez-la pour voir apparaître les <em>Sous-catégories</em>.</li>
                    <li><strong>Important :</strong> N'oubliez pas de cliquer sur <span style="background:#eee; padding: 2px 5px; border-radius:4px; font-size: 12px; color:#333;">Appliquer</span> pour confirmer vos filtres.</li>
                </ul>
            </div>

            <div style="margin-bottom: 25px;">
                <h3 style="color: #1e40af; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px; display: flex; align-items: center;">
                    <i class="fa-solid fa-star" style="margin-right: 10px;"></i> Nos Exclusivités
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div style="background: #f3f4f6; padding: 10px; border-radius: 8px;">
                        <strong style="color: #1f2937; font-size: 14px;">🗳️ Espace Vote</strong>
                        <p style="font-size: 13px; color: #6b7280; margin: 5px 0 10px 0;">Élisez le joueur de l'année.</p>
                        <a href="{{ route('vote.page') }}" style="font-size: 13px; color: #2563eb; text-decoration: underline; font-weight: bold;">Accéder au vote &rarr;</a>
                    </div>
                    <div style="background: #f3f4f6; padding: 10px; border-radius: 8px;">
                        <strong style="color: #1f2937; font-size: 14px;">💼 Compte Pro</strong>
                        <p style="font-size: 13px; color: #6b7280; margin: 5px 0 10px 0;">Offres pour les clubs.</p>
                        <a href="/creer_un_compte_professionnel_1" style="font-size: 13px; color: #2563eb; text-decoration: underline; font-weight: bold;">Devenir Partenaire &rarr;</a>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <h3 style="color: #1e40af; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px; display: flex; align-items: center;">
                    <i class="fa-solid fa-box-open" style="margin-right: 10px;"></i> Mes Commandes
                </h3>
                <p style="font-size: 14px; color: #4b5563; line-height: 1.6;">
                    @auth
                        Vous êtes connecté. Vous pouvez suivre l'état de vos achats directement ici :<br>
                        <a href="{{ route('commande.liste') }}" style="display: inline-block; margin-top: 8px; background-color: #2563eb; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 13px;">
                            <i class="fa-solid fa-truck-fast"></i> Suivre mes commandes
                        </a>
                    @else
                        Pour passer commande ou suivre un colis, vous devez vous connecter.<br>
                        <a href="/connexion" style="color: #2563eb; text-decoration: underline; font-weight: bold;">Se connecter maintenant</a>
                    @endauth
                </p>
            </div>

            <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 15px;">
                <h4 style="margin: 0 0 10px 0; color: #1e40af; font-size: 15px;">❓ Une question sans réponse ?</h4>
                <p style="font-size: 13px; color: #4b5563; margin-bottom: 0;">
                    Notre équipe support est disponible. 
                    <a href="mailto:olivier.brunel@etu.univ-smb.fr" style="color: #2563eb; text-decoration: underline; font-weight: bold;">Contactez le support</a>
                </p>
            </div>

        </div>

        <div style="padding: 15px; text-align: right; background: #f9fafb; border-radius: 0 0 12px 12px; display: flex; justify-content: space-between; align-items: center;">
            <a href="/privacy_policy" style="font-size: 12px; color: #9ca3af; text-decoration: none;">Politique de confidentialité</a>
            
            <button onclick="toggleHelpModal()" style="padding: 10px 20px; background: #4b5563; color: white; border: none; border-radius: 6px; cursor: pointer;">
                Fermer
            </button>
        </div>
    </div>
</div>

<script>
    function toggleHelpModal() {
        const modal = document.getElementById('helpModal');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex'; // Affiche en mode Flexbox pour centrer
        } else {
            modal.style.display = 'none';
        }
    }
    
    // Fermer si on clique en dehors de la boîte blanche
    window.onclick = function(event) {
        const modal = document.getElementById('helpModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
</body>
</html>
