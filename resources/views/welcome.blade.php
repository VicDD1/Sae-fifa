<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FIFA</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="antialiased">

    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex;  width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>


            <!-- CORRECTION : lien Vote propre -->
            <a href="{{ route('vote.page') }}">Votes</a>

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

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; text-align: center;">
            {{ session('success') }}
        </div>
    @endif
    @auth
    @if (Auth::user()->id_user_connecte === 13)
    <a href="/creer_un_produit"><div style="background-color:rgb(164, 163, 202); color: #155724; padding: 15px; text-align: left;"> creation de produit</div></a>
    @endif
    @if (Auth::user()->id_user_connecte === 11)
    <a href="/produits_en_cours"><div style="background-color:rgb(164, 163, 202); color: #155724; padding: 15px; text-align: left;"> voir les produits en cours de creation</div></a>
    @endif
    @endauth
    @include('cookies')
<footer>
    <a href="{{ route('cookies.manage') }}">Gérer mes cookies</a>
        <span>|</span>
    <a href="/privacy_policy"> Conditions d'utilisation </a>
        <span>|</span>
     <a href="/privacy_policy"> Respect de la vie privée </a> 
</footer>
 
    
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
