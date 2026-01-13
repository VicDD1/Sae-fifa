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
        <h2>Gestion des livraisons (Siège)</h2>
        
        <div class="table-container">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">N° Commande</th>
                        <th>Acheteur</th>
                        <th>Statut</th>
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
                        <td>
                            <form action="{{ route('siege.commandes.update', $commande->id_commande) }}" method="POST" style="display: flex; gap: 5px;">
                                @csrf
                                <select name="statut_livraison" style="padding: 5px;">
                                    <option value="Accepté">Accepter</option>
                                    <option value="Refusé">Refuser</option>
                                    <option value="Réserve">Réserve</option>
                                </select>
                                <input type="text" name="commentaire_sav" placeholder="Motif (si besoin)" style="padding: 5px;">
                                <button type="submit" class="btn-primary" style="padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer;">OK</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>