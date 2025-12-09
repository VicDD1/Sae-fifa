<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement de la commande</title>

    <link rel="stylesheet" href="{{ asset('css/confirmation_commande.css') }}">
</head>

<body>

    <div class="container">
        <div class="card">

            <h1>Paiement de votre commande</h1>
            <p class="intro">
                Merci pour votre commande.  
                Veuillez renseigner vos informations bancaires pour finaliser l’achat.
            </p>

            @if(session('success'))
                <p class="success">{{ session('success') }}</p>
            @endif

            <form action="{{ url('/commande/confirmation') }}" method="POST" class="payment-form">
                @csrf

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

                <button type="submit" class="btn-pay">Payer maintenant</button>
            </form>

            <a href="{{ url('/produits') }}" class="return">Retour à la boutique</a>
        </div>
    </div>

</body>
</html>