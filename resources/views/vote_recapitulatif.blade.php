<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <title>Récapitulatif de votre vote</title>
    <link rel="stylesheet" href="{{ asset('css/account_vote_fifa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>
<div id="vote_div">
<div class="card">

    <h1>Récapitulatif de votre vote</h1>
    <p class="description">Merci pour votre vote.</p>

    <h2>Thème :</h2>
    <p>{{ $recap['theme'] ?? 'Thème inconnu' }}</p>

    <h2>Classement des joueurs :</h2>

    @php
        $votes = $recap['votes'] ?? [];
        usort($votes, fn($a, $b) => $a['rank'] <=> $b['rank']);
    @endphp

    <ol>
        @foreach($votes as $v)
            <li>
                {{ $v['rank'] }}ᵉ :
                {{ $v['joueur']->prenom }} {{ $v['joueur']->nom }}
            </li>
        @endforeach
    </ol>

   <a href="{{ url('/') }}" class="btn-cancel">Retour à l'accueil</a>

</div>
</div>

</body>
</html>