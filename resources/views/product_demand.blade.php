<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fifa - Demande Produit</title>
    <link rel="stylesheet" href="{{ asset('css/product_demande.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

</head>

<body>

@include('header')

    <main class="middle">
        <div class="login-box">
            <h2 class="login-title">demande de produit</h2>

            <form method="POST" action="{{ route('registerProduct.step1.post') }}">
                @csrf

                <div class="group">
                    <label class="input-label-produit">Nom du produit proposé</label>
                    <input type="text" name="nom_produit_propose" class="custom-input" value="{{ old('nom_produit_propose') }}" required>
                    @error('nom_produit_propose')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="group">
                    <label class="input-label-produit">Description du produit</label>
                    <input type="text" name="description_produit_propose" value="{{ old('description_produit_propose') }}" class="custom-input" required>
                </div>

                <button type="submit" class="btn-login">créer la proposition</button>
            </form>
        </div>
    </main>

    <footer>
        <a href="#">Conditions d'utilisation</a>
        <span>|</span>
        <a href="/privacy_policy">Respect de la vie privée</a>
    </footer>
@include('botman')
</body>
</html>