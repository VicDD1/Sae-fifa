<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FIFA Store - Accueil</title>
        <link rel="stylesheet" href="{{ asset('css/product.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
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


        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 15px; text-align: center; margin: 20px auto; max-width: 800px; border-radius: 5px;">
                {{ session('success') }}
            </div>
        @endif

        
        <main class="container">
            <h1>Nos Articles</h1>
            
            <h2>Filtres</h2>

            <form action="" method="GET" class="filter-bar">

                <div class="search-container" style="width: 100%; margin-bottom: 20px; position: relative;">
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="Rechercher un produit..." 
                        value="{{ request('search') }}"
                        style="width: 100%; padding: 12px 40px 12px 15px; border: 1px solid #ccc; border-radius: 25px; font-size: 16px;"
                    >
                    <button type="submit" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #045694;">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 18px;"></i>
                    </button>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Équipe / Nation</label>
                    <select name="id_nation" class="filter-input" style="min-width: 180px;">
                        <option value="">Toutes les nations</option>
                        @foreach($nations as $nation)
                            <option value="{{ $nation->id_nation }}" {{ request('id_nation') == $nation->id_nation ? 'selected' : '' }}>
                                {{ $nation->nom_nation }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Catégorie</label>
                    <select name="id_categorie" class="filter-input" style="min-width: 180px;">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id_categorie }}" {{ request('id_categorie') == $categorie->id_categorie ? 'selected' : '' }}>
                                {{ $categorie->label_categorie }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Sous-Catégorie</label>
                    <select name="sous_categorie" class="filter-input" style="min-width: 180px;">
                        <option value="">Toutes</option>
                        @foreach($sous_categories as $sub)
                            <option 
                                value="{{ $sub->id_categorie }}" 
                                {{ request('sous_categorie') == $sub->id_categorie ? 'selected' : '' }}>
                                {{ $sub->label_categorie }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Taille</label>
                    <select name="id_taille" class="filter-input" style="min-width: 100px;">
                        <option value="">Toutes</option>
                        @foreach($tailles as $taille)
                            <option value="{{ $taille->id_taille }}" {{ request('id_taille') == $taille->id_taille ? 'selected' : '' }}>
                                {{ $taille->label_taille }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Couleur</label>
                    <select name="id_colori" class="filter-input" style="min-width: 120px;">
                        <option value="">Toutes</option>
                        @foreach($couleurs as $couleur)
                            <option value="{{ $couleur->id_colori }}" {{ request('id_colori') == $couleur->id_colori ? 'selected' : '' }}>
                                {{ $couleur->label_colori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Budget Max (€)</label>
                    <input type="number" name="max_price" class="filter-input" placeholder="Ex: 80" value="{{ request('max_price') }}" style="width: 100px;">
                </div>

                <div class="filter-group">
                    <label class="filter-label">Trier par</label>
                    <select name="sort" class="filter-input">
                        <option value="">Par défaut</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                    </select>
                </div>

                <button type="submit" class="btn-filter">Appliquer</button>
                <a href="{{ url()->current() }}" class="btn-reset">Effacer</a>
@auth
    @if(Auth::user()->id_user_connecte === 13)
                <a href="{{ route('make_product.create') }}" class="btn-create">Créer un produit</a>

                <a href="{{ route('categorie.create') }}" class="btn-create" style="background-color: #6c757d; margin-left: 5px;">
                    Ajouter une Catégorie
                </a>
                @endif
@endauth
            </form>

<div class="product-grid">
    @if(isset($products) && count($products) > 0)
        @foreach($products as $product)
            <article class="card">
                
                <a href="{{ route('product.detail', $product->id_produit) }}" class="card-img">
                    {{-- DEBUT DE LA MODIFICATION --}}
                    @if($product->photo)
                        {{-- Si une photo existe en base, on utilise son chemin --}}
                        <img src="{{ asset($product->photo->code_photo) }}" 
                             alt="{{ $product->label_produit }}"
                             style="width: 100%; height: auto; object-fit: cover;">
                    @else
                        {{-- Image par défaut si pas de photo --}}
                        <img src="https://via.placeholder.com/300x300?text=Pas+d'image" 
                             alt="Image non disponible">
                    @endif
                    {{-- FIN DE LA MODIFICATION --}}
                </a>

                <div class="card-body">
                    <a href="{{ route('product.detail', $product->id_produit) }}" style="text-decoration:none; color:inherit;">
                        <h2 class="card-title">{{ $product->label_produit }}</h2>
                    </a>
                    
                    <p class="card-desc">{{ Str::limit($product->description_produit, 100) }}</p>

                    <span class="card-price">{{ number_format($product->prix_base, 2) }} €</span>
                </div>
            </article>
        @endforeach
    @else
        <div style="grid-column: 1/-1; text-align:center; padding: 40px; background:#f0f0f0; border-radius: 8px;">
            <p style="font-size: 1.2rem; color: #555;">Aucun produit ne correspond à vos critères.</p>
            <a href="{{ url()->current() }}" style="color: blue; text-decoration: underline;">Voir tout le catalogue</a>
        </div>
    @endif
    </div>
 <div class="history-sidebar">
    <h2>Vus récemment</h2>
    <div class="history-list">
        @forelse($recentProducts as $recent)
            <div class="history-item">
                <a href="{{ route('product.detail', $recent->id_produit) }}" class="history-link">
                    
                    <img src="../assets/photo_produit/{{ $recent->id_produit }}.webp"
                         alt="{{ $recent->label_produit }}"
                         class="history-img">
                    <div class="history-body">     
                        <h4 class="history-label">
                            {{ $recent->label_produit }}
                        </h4>
    
                        <div class="history-price">
                            {{ number_format($recent->prix_base, 2) }} €
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <p class="history-empty">
                Aucun article consulté pour le moment.
            </p>
        @endforelse
    </div>
</div>
        </main>
        
<footer>
    <a href="{{ route('cookies.manage') }}">Gérer mes cookies</a>
        <span>|</span>
    <a href="/privacy_policy"> Conditions d'utilisation </a>
        <span>|</span>
     <a href="/privacy_policy"> Respect de la vie privée </a> 
</footer>

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