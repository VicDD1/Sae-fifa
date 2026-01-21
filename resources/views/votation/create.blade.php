<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une Votation</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/account_vote_fifa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>
<body>
@include('header')

    <div id="vote_div">
        <div class="card">
            <h1>Créer une nouvelle Votation</h1>
            <p class="description">En tant que membre du service vente, vous pouvez créer une nouvelle votation pour les visiteurs.</p>

            {{-- Message de succès --}}
            @if(session('success'))
                <div style="background-color: #00ff87; color: #1a1a2e; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Affichage des erreurs de validation --}}
            @if ($errors->any())
                <div style="color:#b00020; margin-bottom:20px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('theme_vote.store') }}">
                @csrf

                {{-- NOM DU THÈME --}}
                <div class="theme-group">
                    <label for="nom_theme">Nom du thème de votation</label>
                    <input type="text" 
                           name="nom_theme" 
                           id="nom_theme" 
                           value="{{ old('nom_theme') }}" 
                           placeholder="Ex: Joueur trop vieux"
                           style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px;"
                           required>
                </div>

                {{-- DATE DE FIN DE VOTE --}}
                <div class="theme-group" style="margin-top: 20px;">
                    <label for="date_fin_vote">Date de fin du vote</label>
                    <input type="date" 
                           name="date_fin_vote" 
                           id="date_fin_vote" 
                           value="{{ old('date_fin_vote') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px;"
                           required>
                </div>

                {{-- BOUTONS --}}
                <div class="actions" style="margin-top: 30px;">
                    <a href="{{ url('/') }}" class="btn-cancel">Retour</a>
                    <button type="submit" class="btn-send">Créer la votation</button>
                </div>
            </form>
        </div>
    </div>

    @include('botman')
</body>
</html>
