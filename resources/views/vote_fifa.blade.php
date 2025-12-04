<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface de Vote</title>
    
    <link rel="stylesheet" href="/css/account_vote_fifa.css">
    
</head>
<body>

    <div class="card">
        <h1>Session de Vote</h1>
        
        <p class="description">
            Veuillez confirmer les informations du joueur ci-dessous avant de valider votre vote pour cette session.
        </p>

        <form>
            <div class="input-group">
                <label for="theme">Thème</label>
                <input type="text" id="theme" name="theme" value="Culture Générale" readonly>
            </div>

            <div class="input-group">
                <label for="player">Joueur</label>
                <input type="text" id="player" name="player" value="Abdelk" readonly>
            </div>

            <div class="input-group">
                <label for="ranking">Classement Actuel</label>
                <input type="text" id="ranking" name="ranking" value="#1 - Expert" readonly>
            </div>

            <div class="actions">
                <button type="button" class="btn-cancel">RETOUR</button>
                <button type="submit" class="btn-send">VALIDER LE VOTE</button>
            </div>
        </form>
    </div>

</body>
</html>