<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un thème de vote</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #fff;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .card-header {
            background: linear-gradient(90deg, #00ff87 0%, #60efff 100%);
            color: #1a1a2e;
            font-weight: bold;
        }
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #00ff87;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(0, 255, 135, 0.25);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        .form-label {
            color: #00ff87;
            font-weight: 500;
        }
        .btn-submit {
            background: linear-gradient(90deg, #00ff87 0%, #60efff 100%);
            border: none;
            color: #1a1a2e;
            font-weight: bold;
            padding: 12px 30px;
        }
        .btn-submit:hover {
            transform: scale(1.05);
            color: #1a1a2e;
        }
        .joueurs-select {
            max-height: 300px;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
        }
        .joueur-checkbox {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 8px;
            transition: all 0.2s;
        }
        .joueur-checkbox:hover {
            background: rgba(0, 255, 135, 0.2);
        }
        .form-check-input:checked {
            background-color: #00ff87;
            border-color: #00ff87;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex; width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>
            <a href="{{ route('vote.page') }}">Vote</a>
            <a href="/players">Les joueurs</a>
            <a href="/blog">L'Actu</a>
        </nav>
    </header>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0 fs-4">🏆 Créer un nouveau thème de vote</h2>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('theme_vote.store') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="nom_theme" class="form-label">
                                    Titre du thème <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="nom_theme" 
                                    name="nom_theme" 
                                    required 
                                    placeholder="Ex: Ballon d'Or 2026, Joueur de l'année..."
                                    value="{{ old('nom_theme') }}"
                                >
                            </div>

                            <div class="mb-4">
                                <label for="date_fin_vote" class="form-label">
                                    Date de fin du vote <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control" 
                                    id="date_fin_vote" 
                                    name="date_fin_vote" 
                                    required
                                    min="{{ date('Y-m-d') }}"
                                    value="{{ old('date_fin_vote') }}"
                                >
                                <small class="text-muted">La date doit être aujourd'hui ou dans le futur.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    Associer des joueurs (optionnel)
                                </label>
                                <p class="text-muted small">
                                    Vous pouvez associer des joueurs maintenant ou plus tard. 
                                    Un thème sans joueur ne sera pas votable.
                                </p>
                                
                                <div class="joueurs-select">
                                    @forelse($joueurs as $joueur)
                                        <div class="joueur-checkbox">
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    name="joueurs[]" 
                                                    value="{{ $joueur->id_joueur }}" 
                                                    id="joueur_{{ $joueur->id_joueur }}"
                                                    {{ in_array($joueur->id_joueur, old('joueurs', [])) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="joueur_{{ $joueur->id_joueur }}">
                                                    <strong>{{ $joueur->prenom }} {{ $joueur->nom }}</strong>
                                                    <span class="text-muted">- {{ $joueur->club }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted">Aucun joueur disponible.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('theme_vote.index') }}" class="btn btn-outline-secondary">
                                    ← Annuler
                                </a>
                                <button type="submit" class="btn btn-submit">
                                    Créer le thème
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
