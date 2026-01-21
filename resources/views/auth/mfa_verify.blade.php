<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de sécurité</title>
    <link rel="stylesheet" href="css/mfa.css">
</head>
<body>

    <div class="card">
        <div style="font-size: 3rem; margin-bottom: 10px;">🔒</div>
        <h2>Double Authentification</h2>
        <p>Pour protéger votre compte, nous avons envoyé un code de sécurité par SMS au numéro associé à ce compte.</p>

        @if(session('error'))
            <div class="alert-error">
                ❌ {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('mfa.verify') }}" method="POST">
            @csrf
            
            <input type="text" name="code" placeholder="000000" maxlength="6" required autofocus autocomplete="off">
            
            <button type="submit">Vérifier le code</button>
        </form>

        <a href="/connexion" class="cancel-link">Annuler la connexion</a>
    </div>

</body>
</html>