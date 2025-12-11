<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon Panier - FIFA Store</title>

    <link rel="stylesheet" href="{{ asset('css/account_panier.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<header>
    <div class="logo">FIFA Store</div>
    <a href="{{ url('/produits') }}">
        <i class="fa-solid fa-arrow-left"></i> Continuer mes achats
    </a>
</header>


<main class="cart-container">

    <div class="cart-items">
        <h1>Mon Panier</h1>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif


        @if($lignes->count() >= 0)

            @foreach($lignes as $ligne)
                <div class="cart-item">

                    <img 
                        src="../assets/photo_produit/{{ $ligne->id_produit }}.webp"
                        alt="photo {{ $ligne->produit->label_produit }}"
                        style="width:120px; height:auto; border-radius:8px;"
                    >

                    <div class="item-details">
                        
                        <h3>{{ $ligne->produit->label_produit }}</h3>

                        <div class="item-options" style="font-size: 14px; color: #666; margin-bottom: 5px;">
                            Taille :
                            <strong>{{ $ligne->taille->label_taille ?? 'Standard' }}</strong> |
                            Couleur :
                            <strong>{{ $ligne->couleur->label_colori ?? 'Unique' }}</strong>
                        </div>

                        <div class="quantity-controls">
                            <span style="margin-right: 10px; color: #666;">Quantité :</span>

                            <a href="{{ route('panier.update', ['id_ligne' => $ligne->id_ligne, 'action' => 'minus']) }}"
                               class="qty-btn">
                                <i class="fa-solid fa-minus"></i>
                            </a>

                            <span class="qty-number">{{ $ligne->quantitee }}</span>

                            <a href="{{ route('panier.update', ['id_ligne' => $ligne->id_ligne, 'action' => 'plus']) }}"
                               class="qty-btn">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                        </div>

                        <div class="item-price">
                            {{ number_format($ligne->quantitee * $ligne->produit->prix_base, 2) }} €
                        </div>

                        <a href="{{ route('panier.supprimer', $ligne->id_ligne) }}" class="btn-delete">
                            <i class="fa-solid fa-trash"></i> Supprimer
                        </a>

                    </div>
                </div>
            @endforeach

        @else
            <div class="empty-cart">
                <h2>Votre panier est vide.</h2>
                <p>Vous n'avez pas encore ajouté d'articles.</p>

                <a href="{{ url('/produits') }}" class="btn-checkout" style="display:inline-block; width:auto; margin-top:20px;">
                    Retourner à la boutique
                </a>
            </div>
        @endif
    </div>


    @if($lignes->count() > 0)
        <div class="cart-summary">

            <h2 style="font-size: 18px; margin-top: 0;">Récapitulatif</h2>

            @php
                $countItems = $lignes->sum('quantitee');
            @endphp

            <p>Sous-total ({{ $countItems }} articles) :</p>

            <div class="total-price">{{ number_format($total, 2) }} €</div>

            <a href="/commande" class="btn-checkout">Passer la commande</a>
        </div>
    @endif

</main>

</body>
</html>
