<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Qualité Express</title>
    <link rel="stylesheet" href="{{ asset('css/gestion_commande.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-admin">
        <h2><i class="fa-solid fa-truck-fast"></i> Suivi Qualité : Commandes Express</h2>
        
        <div class="table-container">
            <table style="width: 100%; border-collapse: collapse; background: white;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee; background: #f8f9fa;">
                        <th style="padding: 12px;">N° Commande</th>
                        <th>Date de Commande</th>
                        <th>Date de Livraison Réelle</th>
                        <th>Analyse Qualité</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commandesExpress as $commande)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;">#{{ $commande->id_commande }}</td>
                        <td>{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}</td>
                        <td>
                            {{ $commande->date_livraison_reelle ? \Carbon\Carbon::parse($commande->date_livraison_reelle)->format('d/m/Y') : 'En attente de livraison' }}
                        </td>
                        <td>
                            @if($commande->date_livraison_reelle)
                                @php
                                    $diff = \Carbon\Carbon::parse($commande->date_commande)->diffInDays($commande->date_livraison_reelle);
                                @endphp
                                @if($diff <= 1)
                                    <span style="color: green; font-weight: bold;">✅ Conforme ({{ $diff }}j)</span>
                                @else
                                    <span style="color: red; font-weight: bold;">⚠️ Retard ({{ $diff }}j)</span>
                                @endif
                            @else
                                <span style="color: gray italic;">Calcul impossible</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <br>
        <a href="{{ route('siege.commandes.index') }}" style="text-decoration: none; background: #045694; color: white; padding: 10px 20px; border-radius: 5px;">Retour Gestion</a>
    </div>
</body>
</html>