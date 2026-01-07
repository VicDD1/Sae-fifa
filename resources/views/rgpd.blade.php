<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/rgpd.css') }}">
    <title>Gestion RGPD - FIFA Store</title>
</head>
<body>
    <div class="rgpd-container">
        <div class="rgpd-header">
            <h1>Espace Délégué à la Protection des Données (DPO)</h1>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="rgpd-card">
            <h3>Anonymisation des données personnelles</h3>
            <p>Conformément à l'article 17 du RGPD (Droit à l'effacement), vous pouvez anonymiser les comptes inactifs.</p>
            
            <form action="{{ route('rgpd.anonymize') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label>Utilisateurs inactifs depuis le :</label>
                    <input type="date" name="date_limite" value="{{ date('Y-m-d', strtotime('-3 years')) }}">
                </div>

                <button type="submit" class="btn-anonymize" onclick="return confirm('Attention : Cette action transformera les noms et emails en données génériques. Continuer ?')">
                    Exécuter l'anonymisation massive
                </button>
            </form>
        </div>

        <a href="/" style="color: #666; text-decoration: none;">&larr; Retour au site</a>
    </div>
</body>
</html>