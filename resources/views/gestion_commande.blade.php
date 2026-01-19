<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Commande - Siège</title>
    <link rel="stylesheet" href="{{ asset('css/gestion_commande.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <nav>
            <a href="/"> <img style="width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Accueil"></a>
            <a href="/produits">Fifa Store</a>
            <a href="{{ route('vote.page') }}">Vote</a>
            <a href="/blog">L'Actu</a>

            @auth
                @php
                    $panier = \App\Models\Panier::where('id_user_connecte', Auth::id())->first();
                    $totalQuantite = $panier ? $panier->lignes->sum('quantitee') : 0;
                @endphp
                
                {{-- LIEN SPÉCIFIQUE SERVICE COMMANDE POUR ID 62 --}}
                @endauth
                @auth
    @if(Auth::user()->id_user_connecte === 62)
        {{-- On utilise l'URL directe pour être certain qu'il n'y a pas d'erreur de nom de route --}}
        <a href="/siege/commandes" style="font-weight: bold; margin-left: 20px;">
            service commande
        </a>
    @endif
@endauth

            <a href="{{ route('panier.index') }}" style="margin-left: 15px; font-weight: bold;">
                <i class="fa-solid fa-cart-shopping"></i> Mon Panier ({{ $totalQuantite ?? 0 }})
            </a>

            @auth
                <div style="display: inline-flex; align-items: center; margin-left: 20px; color: white;">
                    <a href="/mon-profil" style="text-decoration: none; display: flex; align-items: center; color: white;">
                        <span style="margin-right: 10px; font-weight: bold; border-bottom: 2px solid #00ff87;">
                            {{ Auth::user()->prenom_user_connecte ?? Auth::user()->surnom_user_connecte }}
                        </span>
                        <img style="width:40px;" src="{{asset('assets/iconEdit.png')}}" alt="Profil">
                    </a>

                    <form action="/logout" method="POST" style="display:inline; margin-left: 10px;">
                        @csrf
                        <button type="submit" style="background: none; border: none; cursor: pointer; color: #ffcccc;">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </form>
                </div>
            @endauth
        </nav>
    </header>

    <div class="container-admin">
        @if(session('error'))
    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
@endif
        <h2>Gestion des livraisons (Siège)</h2>
        <div style="margin-bottom: 20px;">
    <a href="{{ route('siege.commandes.qualite') }}" class="btn-primary" style="text-decoration: none; padding: 10px 15px; border-radius: 5px; display: inline-block;">
        <i class="fa-solid fa-chart-line"></i> Voir le Rapport Qualité Express
    </a>
</div>
        <div class="table-container">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
    <tr style="text-align: left; border-bottom: 2px solid #eee;">
        <th style="padding: 10px;">N° Commande</th>
        <th>Acheteur</th>
        <th>Statut</th>
        <th>Échéance Auto</th> 
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @foreach($commandes as $commande)
    <tr style="border-bottom: 1px solid #eee;">
        <td style="padding: 15px;">#{{ $commande->id_commande }}</td>
        <td>ID: {{ $commande->id_acheteur }}</td>
        <td>
            <span class="badge-{{ strtolower($commande->statut_livraison ?? 'encours') }}">
                {{ $commande->statut_livraison ?? 'En cours' }}
            </span>
        </td>
        
        {{-- CALCUL DE L'ÉCHÉANCE --}}
        <td>
            @if($commande->statut_livraison === 'Réserve')
                @php
                    $dateLimite = \Carbon\Carbon::parse($commande->date_commande)->addDays(15);
                    $joursRestants = ceil(now()->diffInDays($dateLimite, false));
                @endphp

                @if($joursRestants > 0)
                    <span style="color: #856404; font-size: 0.9em;">
                        <i class="fa-regular fa-clock"></i> Auto dans <strong>{{ $joursRestants }} jours</strong>
                    </span>
                @else
                    <span style="color: #721c24; font-weight: bold;">Validation imminente</span>
                @endif
            @else
                <span style="color: #ccc;">-</span>
            @endif
        </td>

        <td>
            <form action="{{ route('siege.commandes.update', $commande->id_commande) }}" method="POST" style="display: flex; gap: 5px;">
                @csrf
                <select name="statut_livraison" style="padding: 5px;">
                    <option value="Accepté" {{ $commande->statut_livraison == 'Accepté' ? 'selected' : '' }}>Accepter</option>
                    <option value="Refusé" {{ $commande->statut_livraison == 'Refusé' ? 'selected' : '' }}>Refuser</option>
                    <option value="Réserve" {{ $commande->statut_livraison == 'Réserve' ? 'selected' : '' }}>Réserve</option>
                </select>
                <input type="text" name="commentaire_sav" value="{{ $commande->commentaire_sav }}" placeholder="Motif (si besoin)" style="padding: 5px;">
                <button type="submit" class="btn-primary" style="padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;">OK</button>
            </form>
        </td>
    </tr>
    @endforeach
</tbody>
            </table>
        </div>
    </div>
<script>
    document.querySelectorAll('select[name="statut_livraison"]').forEach(select => {
        select.addEventListener('change', function() {
            // On trouve le champ texte qui est juste à côté (le voisin)
            const inputMotif = this.nextElementSibling; 
            
            if (this.value === 'Refusé' || this.value === 'Réserve') {
                inputMotif.style.border = "2px solid red";
                inputMotif.placeholder = "MOTIF OBLIGATOIRE !";
                inputMotif.required = true;
            } else {
                inputMotif.style.border = "1px solid #ccc";
                inputMotif.placeholder = "Motif (si besoin)";
                inputMotif.required = false;
            }
        });
    });
</script>
</body>
</html>