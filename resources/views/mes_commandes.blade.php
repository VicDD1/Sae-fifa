<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes commandes</title>

    <link rel="stylesheet" href="{{ asset('css/mes_commandes.css') }}">
</head>

<body>

    <div class="commande-container">

        <h1 class="commande-title">Mes commandes</h1>

        @if($commandes->isEmpty())
            <p class="commande-empty">Vous n'avez encore passé aucune commande.</p>
        @else
            <div class="commande-liste">

                @foreach($commandes as $commande)
                    <div class="commande-card">

                        <div class="commande-header">
                            <h2>Commande #{{ $commande->id_commande }}</h2>
                            <span class="commande-date">
                                {{ $commande->date_commande }}
                            </span>
                        </div>

                        <div class="commande-body">
                            <p><strong>Montant total :</strong> {{ number_format($commande->montant_total, 2) }} €</p>
                            <p><strong>Mode de paiement :</strong> {{ $commande->mode_paiement }}</p>
                            <p><strong>Statut :</strong> {{ $commande->statut_paiement }}</p>
                        </div>

                    </div>
                @endforeach

            </div>
        @endif

    </div>

</body>
</html>
