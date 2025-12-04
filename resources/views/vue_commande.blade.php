<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Passer une commande - FIFA Store</title>

    <link rel="stylesheet" href="{{ asset('css/vue_commande.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<header class="header">
    <div class="logo">FIFA Store</div>
    <a href="{{ url('/panier') }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Retour au panier
    </a>
</header>

<main class="checkout-container">

    <!-- INFORMATIONS DE LIVRAISON -->
    <section class="checkout-box">
        <h1 class="title">Informations de livraison</h1>

        <form action="{{ route('commande.valider') }}" method="POST">
            @csrf

            <div class="input-group">
                <label>Nom complet</label>
                <input type="text" name="nom" required>
            </div>

            <div class="input-group">
                <label>Adresse</label>
                <input type="text" name="adresse" required>
            </div>

            <div class="input-group">
                <label>Ville</label>
                <input type="text" name="ville" required>
            </div>

            <div class="input-group">
                <label>Code postal</label>
                <input type="text" name="cp" required>
            </div>

            <div class="input-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" required>
            </div>

            <h2 class="subtitle">Méthode de paiement</h2>

            <div class="payment-options">
                <label><input type="radio" name="paiement" value="carte" checked> Carte bancaire</label>

            </div>

            <button type="submit" class="btn-submit">Valider la commande</button>

        </form>
    </section>

    <!-- RÉCAPITULATIF -->
    <aside class="summary-box">
        <h2 class="summary-title">Récapitulatif</h2>

        @foreach($lignes as $ligne)
        <div class="summary-item">
            <img src="../assets/photo_produit/{{ $ligne->id_produit }}.webp" alt="">
            <div>
                <strong>{{ $ligne->produit->label_produit }}</strong><br>
                Taille : {{ $ligne->taille->label_taille ?? 'Unique' }} |
                Couleur : {{ $ligne->couleur->label_colori ?? 'Par default' }}
                <br>
                Quantité : {{ $ligne->quantitee }}
            </div>
        </div>
        @endforeach

        <div class="total-box">
            <span>Total :</span>
            <span class="total-price">{{ number_format($total, 2) }} €</span>
        </div>
    </aside>

</main>

</body>
</html>
