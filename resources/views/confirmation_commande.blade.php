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

        <!-- INFORMATIONS CLIENT -->
        <div class="section-title">Informations de livraison</div>
        <div class="info-box">
            <p><strong>Nom :</strong> {{ $data['nom'] }}</p>
            <p><strong>Adresse :</strong> {{ $data['adresse'] }}</p>
            <p><strong>Ville :</strong> {{ $data['ville'] }} — {{ $data['cp'] }}</p>
            <p><strong>Téléphone :</strong> {{ $data['telephone'] }}</p>
        </div>

        <!-- MODE DE LIVRAISON -->
        <div class="section-title">Mode de livraison</div>
        <div class="info-box">
            <p><strong>Type :</strong> {{ $mode->type_livraison }}</p>
            <p><strong>Coût :</strong> {{ number_format($mode->prix_mode_livraison, 2, ',', ' ') }} €</p>
        </div>

        <!-- MODE DE PAIEMENT -->
        <div class="section-title">Mode de paiement</div>
        <p><strong>{{ ucfirst($data['paiement']) }}</strong></p>

        <!-- RÉCAPITULATIF TOTAL -->
        <div class="section-title">Montant total</div>

        <div class="total-block">
            <div class="total-row">
                <span>Total articles :</span>
                <strong>{{ number_format($total - $mode->prix_mode_livraison, 2, ',', ' ') }} €</strong>
            </div>

            <div class="total-row">
                <span>Livraison :</span>
                <strong>{{ number_format($mode->prix_mode_livraison, 2, ',', ' ') }} €</strong>
            </div>

            <div class="total-final">
                Total final : {{ number_format($total, 2, ',', ' ') }} €
            </div>
        </div>

        <!-- CONTENU PANIER -->
        <div class="section-title">Votre panier</div>
        <ul>
            @foreach($lignes as $ligne)
                <li>
                    {{ $ligne->produit->label_produit }}
                    (x{{ $ligne->quantitee }}) —
                    {{ number_format($ligne->produit->prix_base, 2, ',', ' ') }} €
                </li>
            @endforeach
        </ul>

        <!-- FORMULAIRE DE PAIEMENT -->
        <form action="{{ route('commande.succes') }}" method="POST" class="payment-form">
            @csrf

            <!-- Données personnelles -->
            <input type="hidden" name="nom" value="{{ $data['nom'] }}">
            <input type="hidden" name="adresse" value="{{ $data['adresse'] }}">
            <input type="hidden" name="ville" value="{{ $data['ville'] }}">
            <input type="hidden" name="cp" value="{{ $data['cp'] }}">
            <input type="hidden" name="telephone" value="{{ $data['telephone'] }}">
            <input type="hidden" name="paiement" value="{{ $data['paiement'] }}">
            <input type="hidden" name="mode_livraison" value="{{ $mode->id_mode_livraison }}">

            <label>Titulaire de la carte</label>
            <input type="text" name="card_name"
                required
                minlength="2"
                maxlength="60"
                pattern="^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$"
                title="Nom invalide. Caractères autorisés : lettres, espaces, tirets.">

            <label>Numéro de carte bancaire</label>
            <input type="text" name="card_number"
                required
                pattern="^[0-9]{16}$"
                maxlength="16"
                inputmode="numeric"
                title="Le numéro doit contenir 16 chiffres.">

            <div class="row">
                <div class="col">
                    <label>Date d’expiration</label>
                    <input type="text" name="expiry"
                        required
                        pattern="^(0[1-9]|1[0-2])\/([0-9]{2})$"
                        maxlength="5"
                        placeholder="MM/AA"
                        title="Format requis : MM/AA.">
                </div>

                <div class="col">
                    <label>CVV</label>
                    <input type="text" name="cvv"
                        required
                        pattern="^[0-9]{3}$"
                        maxlength="3"
                        title="Le code CVV doit contenir 3 chiffres.">
                </div>
            </div>


            <button type="submit" class="btn-pay">Confirmer et payer</button>
        </form>

    </div>
</div>

</body>
</html>
