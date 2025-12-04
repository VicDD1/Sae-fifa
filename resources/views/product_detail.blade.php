<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $product->label_produit }} - FIFA Store</title>

    <link rel="stylesheet" href="{{ asset('css/product.css') }}">

    <style>
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .product-detail { display: flex; gap: 40px; margin-top: 30px; }
        .img-box img { width: 100%; max-width: 500px; border-radius: 10px; }
        .info-box { flex: 1; }
        .price { font-size: 24px; color: #b12727; font-weight: bold; margin: 20px 0; }
        .desc { line-height: 1.6; color: #555; font-size: 18px; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: #333; text-decoration: underline; }
        .choice label { display:block; margin-top:5px; }
    </style>
</head>

<body>

<header>
    <div class="logo">FIFA Store</div>  
        @auth
            @php
                $panier = \App\Models\Panier::where('id_user_connecte', Auth::id())->first();
                $totalQuantite = $panier ? $panier->lignes->sum('quantitee') : 0;
            @endphp
        @endauth

        @guest
            @php $totalQuantite = 0; @endphp
        @endguest

            @guest
                <a href="/connexion" class="account_creation">
                <img src="{{ asset('assets/icone.png') }}" alt="Compte">
             </a>
        @endguest
        @auth
            <div style="display: inline-flex; align-items: center; margin-left: 20px; color: white;">        
                <a href="/mon-profil" style="text-decoration: none; display: flex; align-items: center;">
                    <span style="margin-right: 10px; font-weight: bold; border-bottom: 2px solid #00ff87;">
                        {{ Auth::user()->prenom_user_connecte ?? Auth::user()->surnom_user_connecte }}
                    </span>
                </a>
                <form action="/logout" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" title="Se déconnecter"
                            style="background: none; border: none; cursor: pointer; color: #ffcccc;">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </form>
            </div>
        @endauth
    <a href="/panier" class="nav-link" style="font-weight: bold;">
        <i class="fa-solid fa-cart-shopping"></i> Mon Panier ({{ $totalQuantite }})
    </a>
</header>

<div class="container">

    <div class="product-detail">

        <!-- IMAGE PRODUIT -->
        <div class="img-box">
            <img src="../assets/photo_produit/{{$product->id_produit}}.webp" alt="Image produit">
        </div>

        <div class="info-box">

            <!-- NOM PRODUIT -->
            <h1>{{ $product->label_produit }}</h1>

            <!-- PRIX -->
            <div class="price">{{ number_format($product->prix_base, 2) }} €</div>

            <!-- FORMULAIRE AJOUT PANIER -->
            @auth
            <form action="{{ route('panier.ajouter', $product->id_produit) }}" method="GET" style="margin-top:20px;">

                <!-- ID PRODUIT -->
                <input type="hidden" name="id_produit" value="{{ $product->id_produit }}">

                <div class="choice">

                    <!-- COULEURS -->
                    <div style="margin-bottom:15px;">
                        <strong>Couleur :</strong><br>

                        @if($product->couleurs && $product->couleurs->count() > 0)
                            @foreach($product->couleurs as $index => $couleur)
                                <label>
                                    <input type="radio" 
                                        name="id_colori" 
                                        value="{{ $couleur->id_colori }}" 
                                        {{ $index === 0 ? 'checked' : '' }}>
                                    {{ $couleur->label_colori }}
                                </label>
                            @endforeach
                        @else
                            <span>Unique</span>
                        @endif

                    </div>

                    <!-- TAILLES -->
                    @if($product->tailles && $product->tailles->count() > 0)
                        @foreach($product->tailles as $index => $taille)
                            <label>
                                <input type="radio" 
                                    name="id_taille" 
                                    value="{{ $taille->id_taille }}" 
                                    {{ $index === 0 ? 'checked' : '' }}>
                                {{ $taille->label_taille }}
                            </label>
                        @endforeach
                    @else
                        <span>Standard</span>
                    @endif


                </div>

                <!-- DESCRIPTION -->
                <h3>Description</h3>
                <p class="desc">
                    {{ $product->description_produit }}
                </p>

                <br>
                <input type="hidden" name="image" value="../assets/photo_produit/{{ $product->id_produit }}.webp">

                <!-- BOUTON AJOUT PANIER -->
                <button type="submit" 
                style="background:#333; color:white; padding:15px 30px;
                    border:none; border-radius:5px; cursor:pointer; margin-right:10px;">
                    Ajouter au panier
                </button>


                <a href="/panier"
                style="background:#555; color:white; padding:15px 30px; 
                        border-radius:5px; text-decoration:none;">
                    Voir mon panier
                </a>


            </form>
            @endauth
            @guest

                <h3>Description</h3>
                <p class="desc">
                    {{ $product->description_produit }}
                </p>
                <a href="/connexion" 
                    class="btn" 
                    style="width: 100%; cursor: pointer; background:#aaa;">
                    Se connecter pour acheter
                </a>
            @endguest                                                        
            
        </div>
    </div>
</div>
<hr style="margin:50px 0;">

<div class="container">
    <h2 style="margin-bottom:20px;">Articles similaires</h2>

    <div style="display:flex; gap:20px; overflow-x:auto; padding-bottom:10px;">

        @forelse($similarProducts as $sim)
            <div style="min-width:200px; border:1px solid #ddd; border-radius:8px; padding:10px;">

                <a href="{{ route('product.detail', $sim->id_produit) }}"
                   style="text-decoration:none; color:inherit;">

                    <img src="../assets/photo_produit/{{ $sim->id_produit }}.webp"
                         alt="{{ $sim->label_produit }}"
                         style="width:100%; height:200px; object-fit:cover; border-radius:6px;">

                    <h4 style="margin:10px 0; font-size:16px;">
                        {{ $sim->label_produit }}
                    </h4>

                    <div style="font-weight:bold; color:#b12727;">
                        {{ number_format($sim->prix_base, 2) }} €
                    </div>
                </a>

            </div>
        @empty
            <p>Aucun article similaire disponible.</p>
        @endforelse

    </div>
</div>


</body>
</html>
