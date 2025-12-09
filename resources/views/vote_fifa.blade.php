<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session de Vote</title>

    <link rel="stylesheet" href="/css/account_vote_fifa.css">
</head>
<body>

    <div class="card">
        <h1>Session de Vote</h1>

        {{-- Message de succès --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Message d’erreur validation --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="description">
            Veuillez sélectionner les informations ci-dessous avant de valider votre vote.
        </p>

        <form method="POST" action="{{ route('vote.submit') }}">
            @csrf

            {{-- LISTE DES THÈMES --}}
            <div class="input-group">
                <label for="theme">Thème</label>
                <select id="theme" name="theme" required>
                    <option value="">-- Sélectionnez un thème --</option>
                    @foreach($themes as $theme)
                        <option value="{{ $theme->id_theme }}">{{ $theme->nom_theme }}</option>
                    @endforeach
                </select>
            </div>

            {{-- LISTE DES JOUEURS --}}
            <div class="input-group">
                <label for="player">Joueur</label>
                <select id="player" name="player" required>
                    <option value="">-- Sélectionnez un joueur --</option>
                    @foreach($players as $player)
                        <option value="{{ $player->id_joueur }}">
                            {{ $player->prenom }} {{ $player->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- LISTE DES CLASSEMENTS --}}
            <div class="input-group">
                <label for="ranking">Classement actuel</label>
                <select id="ranking" name="ranking" required>
                    <option value="">-- Sélectionnez un classement --</option>
                    @foreach($rankings as $ranking)
                        <option value="{{ $ranking }}">{{ $ranking }}</option>
                    @endforeach
                </select>
            </div>

            <div class="actions">
                <button type="button" class="btn-cancel" onclick="history.back()">RETOUR</button>
                <button type="submit" class="btn-send">VALIDER LE VOTE</button>
            </div>

        </form>
    </div>

</body>
</html>
