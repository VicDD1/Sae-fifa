<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="css/email_form.css">
        
    
</head>
<body>

    <div class="card">
        <h2>Récupération de compte</h2>

        {{-- Zone des messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <label for="email">Votre adresse e-mail</label>
            <input type="email" id="email" name="email" placeholder="exemple@email.com" required autofocus>
            
            <button type="submit">Envoyer le lien de secours</button>
        </form>

        <a href="/connexion" class="back-link">Retour à la connexion</a>
    </div>

</body>
</html>