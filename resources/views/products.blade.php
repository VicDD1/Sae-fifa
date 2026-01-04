<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FIFA Store - Accueil</title>
        <link rel="stylesheet" href="{{ asset('css/product.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>

        <header>
        <nav>
            <a href="/">Aceuil</a>

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
            </form>

            <div class="product-grid">
                
                @if(isset($products) && count($products) > 0)
                    @foreach($products as $product)
                        <article class="card">
                            
                            <a href="{{ route('product.detail', $product->id_produit) }}" class="card-img">
                            <img src="{{ asset($product->photo->code_photo ?? 'assets/photo_produit/33.webp') }}">
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
        </main>

    </body>
</html>