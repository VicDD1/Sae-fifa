<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historique Expéditions - FIFA</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="antialiased">

    <header>
        <nav>
            <a href="/"> <img style="width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Accueil"></a>
            <a href="/produits">Fifa Store</a>
            
            @auth
            @if (Auth::user()->id_user_connecte == 13)
                <div style="display: flex; gap: 10px; margin-left: 20px;">
                    <a href="{{ route('service_vente.commandes') }}" class="account_creation">En cours</a>
                    <a href="{{ route('expedition.demain') }}" class="account_creation">Demain</a>
                    <a href="{{ route('expedition.historique') }}" class="account_creation" style="background: #e5e7eb; color: #374151; border: 1px solid #9ca3af;">
                        <i class="fa-solid fa-clock-rotate-left"></i> Historique
                    </a>
                </div>
            @endif
            @endauth
             
             <form action="/logout" method="POST" style="margin-left:auto; margin-right: 20px;">
                @csrf
                <button type="submit" style="background:none; border:none; color:white; cursor:pointer;"><i class="fa-solid fa-power-off"></i></button>
            </form>
        </nav>
    </header>

    <div style="padding: 40px; max-width: 1200px; margin: 0 auto; min-height: 60vh;">
        
        <div style="margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 15px;">
            <h1 style="margin: 0; color: #4b5563;">
                <i class="fa-solid fa-clipboard-check"></i> Historique des envois
            </h1>
            <p style="color: gray;">Liste des {{ $commandesEnvoyees->count() }} commandes qui ont déjà quitté l'entrepôt.</p>
        </div>

        <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                    <tr>
                        <th style="padding: 15px; text-align: left; color: #374151;">N°</th>
                        <th style="padding: 15px; text-align: left; color: #374151;">Client</th>
                        <th style="padding: 15px; text-align: left; color: #374151;">Date Commande</th>
                        <th style="padding: 15px; text-align: center; color: #374151;">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commandesEnvoyees as $commande)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 15px; font-weight: bold; color: #6b7280;">#{{ $commande->id_commande }}</td>
                            <td style="padding: 15px;">
                                {{ $commande->user->prenom_user_connecte ?? 'Client' }} {{ $commande->user->nom_user_connecte ?? '' }}
                            </td>
                            <td style="padding: 15px;">
                                {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <span style="background-color: #d1fae5; color: #065f46; padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; border: 1px solid #a7f3d0;">
                                    <i class="fa-solid fa-check"></i> EXPÉDIÉE
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 50px; text-align: center; color: #94a3b8;">
                                Aucun historique pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="{{ route('service_vente.commandes') }}" style="color: #2563eb; text-decoration: none;">
                &larr; Retour aux commandes en cours
            </a>
        </div>
    </div>
</body>
</html>