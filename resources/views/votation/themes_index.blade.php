<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des thèmes de vote</title>
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
        .table {
            color: #fff;
        }
        .table th {
            border-color: rgba(255, 255, 255, 0.2);
        }
        .table td {
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
        .btn-create {
            background: linear-gradient(90deg, #00ff87 0%, #60efff 100%);
            border: none;
            color: #1a1a2e;
            font-weight: bold;
        }
        .btn-create:hover {
            transform: scale(1.05);
            color: #1a1a2e;
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

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0 fs-4">📊 Gestion des thèmes de vote</h2>
                        <a href="{{ route('theme_vote.create') }}" class="btn btn-create">
                            + Créer un thème
                        </a>
                    </div>

                    <div class="card-body">
                        @if($themes->isEmpty())
                            <div class="text-center py-5">
                                <p class="text-muted mb-3">Aucun thème de vote n'a été créé.</p>
                                <a href="{{ route('theme_vote.create') }}" class="btn btn-create">
                                    Créer le premier thème
                                </a>
                            </div>
                        @else
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Titre du thème</th>
                                        <th>Date de fin</th>
                                        <th>Joueurs</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($themes as $theme)
                                        <tr>
                                            <td>{{ $theme->id_theme }}</td>
                                            <td><strong>{{ $theme->nom_theme }}</strong></td>
                                            <td>
                                                @if($theme->date_fin_vote)
                                                    {{ \Carbon\Carbon::parse($theme->date_fin_vote)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">Non définie</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $theme->joueurs_count }} joueur(s)</span>
                                            </td>
                                            <td>
                                                @if($theme->isExpired())
                                                    <span class="badge badge-expire">Expiré</span>
                                                @elseif($theme->joueurs_count == 0)
                                                    <span class="badge badge-non-votable">Non votable (aucun joueur)</span>
                                                @else
                                                    <span class="badge badge-votable">Votable</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('theme_vote.show', $theme->id_theme) }}" class="btn btn-sm btn-success">
                                                    Gérer
                                                </a>
                                                <form action="{{ route('theme_vote.destroy', $theme->id_theme) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce thème ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        Supprimer
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
