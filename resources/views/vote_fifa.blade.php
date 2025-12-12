<link rel="stylesheet" href="{{ asset('css/account_vote_fifa.css') }}">

@if(session('erreur_vote'))
    <div style="color: red; font-weight:bold; margin-bottom:20px;">
        {{ session('erreur_vote') }}
    </div>
@endif

<form action="{{ route('vote.submit') }}" method="POST">
    @csrf

    <div class="card">
        <h1>Vote FIFA</h1>
        <p class="description">Veuillez sélectionner un thème, les joueurs et leur classement.</p>

        <!-- Thème -->
        <div class="theme-group">
            <label for="theme">Thème</label>
            <select name="theme" id="theme">
                <option value="">-- Sélectionnez un thème --</option>
                @foreach($themes as $theme)
                    <option value="{{ $theme->id_theme }}">{{ $theme->nom_theme }}</option>
                @endforeach
            </select>
        </div>

        <!-- JOUEURS + CLASSEMENTS -->
        <div class="row">

            @for($i = 1; $i <= 4; $i++)
                <div class="col-md-3">
                    <label for="joueur{{ $i }}">Joueur {{ $i }}</label>
                    <select name="joueur{{ $i }}" id="joueur{{ $i }}">
                        <option value="">-- Sélectionnez un joueur --</option>
                        @foreach($joueurs as $joueur)
                            <option value="{{ $joueur->id_joueur }}">{{ $joueur->nom }}</option>
                        @endforeach
                    </select>

                    <label class="classement-label" for="classement{{ $i }}">
                        Classement Joueur {{ $i }}
                    </label>
                    <select name="classement{{ $i }}" id="classement{{ $i }}">
                        <option value="">-- Sélectionnez un classement --</option>
                        <option value="1">1er</option>
                        <option value="2">2ème</option>
                        <option value="3">3ème</option>
                        <option value="4">4ème</option>
                    </select>
                </div>
            @endfor

        </div>

        <!-- Boutons -->
        <div class="actions">
            <a href="{{ url('/') }}" class="btn-cancel">Retour</a>
            <button type="submit" class="btn-send">Valider</button>
        </div>
    </div>

</form>

<script src="{{ asset('js/vote.js') }}"></script>
