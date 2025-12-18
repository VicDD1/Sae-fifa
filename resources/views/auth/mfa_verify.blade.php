<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de sécurité</title>
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
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 { color: #1f2937; margin-bottom: 10px; }
        p { color: #6b7280; font-size: 0.95rem; margin-bottom: 25px; line-height: 1.5; }
        
        input[type="text"] {
            font-size: 2rem;
            letter-spacing: 0.5rem; /* Espace les chiffres */
            text-align: center;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus { border-color: #2563eb; }
        
        button {
            width: 100%;
            padding: 14px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover { background-color: #1d4ed8; }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid #fecaca;
        }
        
        .cancel-link { display: block; margin-top: 20px; color: #9ca3af; text-decoration: none; font-size: 0.9rem; }
        .cancel-link:hover { color: #6b7280; }
    </style>
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