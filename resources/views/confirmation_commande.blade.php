<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande</title>

    <link rel="stylesheet" href="{{ asset('css/confirmation_commande.css') }}">
</head>

<body>

<div class="container">
    <div class="card">

        <h1>Confirmation de votre commande</h1>
        <p class="intro">
            Merci. Vérifiez les informations ci-dessous avant de finaliser votre paiement.
        </p>

        @if(!isset($data))
            <p style="color:red;">Erreur : aucune donnée transmise.</p>
        @endif

        <h2>Informations de livraison</h2>
        <div class="info-box">
            <p><strong>Nom :</strong> {{ $data['nom'] }}</p>
            <p><strong>Adresse :</strong> {{ $data['adresse'] }}</p>
            <p><strong>Ville :</strong> {{ $data['ville'] }} — {{ $data['cp'] }}</p>
            <p><strong>Téléphone :</strong> {{ $data['telephone'] }}</p>
        </div>

        <h2>Mode de paiement choisi</h2>
        <p><strong>{{ $data['paiement'] }}</strong></p>

        <h2>Montant total</h2>
        <p><strong>{{ number_format($total, 2, ',', ' ') }} €</strong></p>

        <h2>Votre panier</h2>
        <ul>
            @foreach($lignes as $ligne)
                <li>
                    {{ $ligne->produit->nom_produit }}  
                    (x{{ $ligne->quantitee }}) —  
                    {{ number_format($ligne->produit->prix_base, 2, ',', ' ') }} €
                </li>
            @endforeach
        </ul>

        <!-- FORMULAIRE FINAL : POST vers confirmation() -->
        <form action="{{ route('commande.confirmation') }}" method="GET" class="payment-form">
            @csrf

            <!-- Données personnelles -->
            <input type="hidden" name="nom" value="{{ $data['nom'] }}">
            <input type="hidden" name="adresse" value="{{ $data['adresse'] }}">
            <input type="hidden" name="ville" value="{{ $data['ville'] }}">
            <input type="hidden" name="cp" value="{{ $data['cp'] }}">
            <input type="hidden" name="telephone" value="{{ $data['telephone'] }}">
            <input type="hidden" name="paiement" value="{{ $data['paiement'] }}">

            <!-- Champs carte bancaire -->
            <label>Titulaire de la carte</label>
            <input type="text" name="card_name" placeholder="Nom sur la carte" required>

            <label>Numéro de carte bancaire</label>
            <input type="text" name="card_number" maxlength="16" placeholder="1234 5678 9012 3456" required>

            <div class="row">
                <div class="col">
                    <label>Date d’expiration</label>
                    <input type="text" name="expiry" maxlength="5" placeholder="MM/AA" required>
                </div>

                <div class="col">
                    <label>CVV</label>
                    <input type="text" name="cvv" maxlength="3" placeholder="123" required>
                </div>
            </div>

            <button type="submit" class="btn-pay">Confirmer et payer</button>
        </form>

    </div>
</div>

</body>
</html>
