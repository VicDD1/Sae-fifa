<link rel="stylesheet" href="{{ asset('css/account_vote_fifa.css') }}">

<div class="card">
    <h1>Récapitulatif de votre vote</h1>
    <p class="description">Merci pour votre vote.</p>

    <h2>Thème :</h2>
    <p>{{ $recap['theme']->nom_theme }}</p>

    <h2>Classement des joueurs :</h2>
    <ol>
        @foreach($recap['votes'] as $vote)
            <li>{{ $vote['classement'] }}ᵉ : {{ $vote['joueur']->nom }}</li>
        @endforeach
    </ol>

    <div class="actions">
        <a href="{{ url('/') }}" class="btn-cancel">Retour à l'accueil</a>
    </div>
</div>
