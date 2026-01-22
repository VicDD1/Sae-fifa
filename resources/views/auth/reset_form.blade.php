<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe</title>
    <link rel="stylesheet" href="css/reset.css">
</head>
<body>

    <div class="card">
        <h2>Définir le nouveau mot de passe</h2>

        {{-- Ici on affiche seulement les erreurs (le succès redirige vers le login) --}}
        @if(session('error'))
            <div class="alert alert-error">
                ❌ {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label>Confirmation de l'email</label>
            <input type="email" name="email" required placeholder="Votre email">

            <label>Nouveau mot de passe</label>
            <input type="password" name="password" required placeholder="••••••••••••">
            <span class="hint">Min. 12 caractères, Majuscule, Minuscule, Chiffre & Symbole.</span>

            <label>Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" required placeholder="••••••••••••">

            <button type="submit">Sauvegarder et se connecter</button>
        </form>
    </div>

</body>
</html>