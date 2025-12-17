<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* min-height pour éviter de couper sur petits écrans */
            margin: 0;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 { text-align: center; color: #333; margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 5px; color: #4b5563; font-size: 0.9em; font-weight: 600; }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            box-sizing: border-box;
        }
        input:focus { outline: 2px solid #2563eb; border-color: transparent; }
        button {
            width: 100%;
            padding: 12px;
            background-color: #16a34a; /* Vert pour valider */
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover { background-color: #15803d; }
        
        /* Les Alertes */
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; text-align: center;}
        .alert-error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .hint { font-size: 0.75rem; color: #6b7280; margin-top: -10px; margin-bottom: 15px; display: block; }
    </style>
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