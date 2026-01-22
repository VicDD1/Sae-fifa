<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $theme->nom_theme }} - Gestion du thème</title>
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
        .card-header-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .table {
            color: #fff;
        }
        .table th, .table td {
            border-color: rgba(255, 255, 255, 0.1);
        }
        .badge-votable {
            background: linear-gradient(90deg, #00ff87 0%, #60efff 100%);
            color: #1a1a2e;
        }
        .badge-non-votable {
            background: #dc3545;
        }
        .badge-expire {
            background: #6c757d;
        }
        .btn-add {
            background: linear-gradient(90deg, #00ff87 0%, #60efff 100%);
            border: none;
            color: #1a1a2e;
            font-weight: bold;
        }
        .btn-add:hover {
            transform: scale(1.02);
            color: #1a1a2e;
        }
        .joueurs-select {
            max-height: 250px;
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
        .theme-info {
            background: rgba(0, 255, 135, 0.1);
            border-left: 4px solid #00ff87;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 20px;
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
            <div class="col-md-10">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Informations du thème -->
                <div class="theme-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">🏆 {{ $theme->nom_theme }}</h2>
                            <p class="mb-0">
                                <strong>Date de fin :</strong> 
                                @if($theme->date_fin_vote)
                                    {{ \Carbon\Carbon::parse($theme->date_fin_vote)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Non définie</span>
                                @endif
                                &nbsp;|&nbsp;
                                <strong>Statut :</strong>
                                @if($theme->isExpired())
                                    <span class="badge badge-expire">Expiré</span>
                                @elseif($joueursAssocies->count() == 0)
                                    <span class="badge badge-non-votable">Non votable (aucun joueur)</span>
                                @else
                                    <span class="badge badge-votable">Votable</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('theme_vote.index') }}" class="btn btn-outline-light">
                                ← Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-7">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="mb-0 fs-5">⚽ Joueurs associés ({{ $joueursAssocies->count() }})</h4>
                            </div>
                            <div class="card-body">
                                @if($joueursAssocies->isEmpty())
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-0">Aucun joueur n'est associé à ce thème.</p>
                                        <p class="text-warning small">⚠️ Un thème sans joueur n'est pas votable.</p>
                                    </div>
                                @else
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Joueur</th>
                                                <th>Club</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($joueursAssocies as $joueur)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $joueur->prenom }} {{ $joueur->nom }}</strong>
                                                    </td>
                                                    <td>{{ $joueur->club }}</td>
                                                    <td>
                                                        <form action="{{ route('theme_vote.retirer_joueur', [$theme->id_theme, $joueur->id_joueur]) }}" method="POST" class="d-inline" onsubmit="return confirm('Retirer ce joueur du thème ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                Retirer
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Ajouter des joueurs -->
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header card-header-secondary">
                                <h4 class="mb-0 fs-5">➕ Ajouter des joueurs</h4>
                            </div>
                            <div class="card-body">
                                @if($joueursDisponibles->isEmpty())
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-0">Tous les joueurs sont déjà associés à ce thème.</p>
                                    </div>
                                @else
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form action="{{ route('theme_vote.associer_joueurs', $theme->id_theme) }}" method="POST">
                                        @csrf

                                        <div class="joueurs-select mb-3">
                                            @foreach($joueursDisponibles as $joueur)
                                                <div class="joueur-checkbox">
                                                    <div class="form-check">
                                                        <input 
                                                            class="form-check-input" 
                                                            type="checkbox" 
                                                            name="joueurs[]" 
                                                            value="{{ $joueur->id_joueur }}" 
                                                            id="joueur_{{ $joueur->id_joueur }}"
                                                        >
                                                        <label class="form-check-label" for="joueur_{{ $joueur->id_joueur }}">
                                                            <strong>{{ $joueur->prenom }} {{ $joueur->nom }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $joueur->club }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="submit" class="btn btn-add w-100">
                                            Ajouter les joueurs sélectionnés
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
