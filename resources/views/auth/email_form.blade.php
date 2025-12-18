<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 { color: #333; margin-bottom: 1.5rem; }
        label { display: block; text-align: left; margin-bottom: 5px; color: #666; font-size: 0.9em; }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box; /* Important pour que le padding ne casse pas la largeur */
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover { background-color: #1d4ed8; }
        
        /* Les Alertes */
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; }
        .alert-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .back-link { display: block; margin-top: 15px; color: #666; text-decoration: none; font-size: 0.9em; }
        .back-link:hover { text-decoration: underline; }
    </style>
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