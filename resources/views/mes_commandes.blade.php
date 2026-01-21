<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes commandes</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/mes_commandes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>


@include('header')
    <div class="commande-container">
    @if($commandes->isEmpty())
        <p class="commande-empty">Vous n'avez encore passé aucune commande.</p>
    @else
        <div class="commande-liste">

            @foreach($commandes as $commande)
                <div class="commande-card">

                    <div class="commande-header toggle-commande">
                        <div>
                            <h2>Commande #{{ $commande->id_commande }}</h2>
                            <span class="commande-date">
                                {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <span class="toggle-icon">+</span>
                    </div>

                    <div class="commande-body">
                        <p><strong>Montant total :</strong> {{ number_format($commande->montant_total, 2) }} €</p>
                        <p><strong>Mode de paiement :</strong> {{ ucfirst($commande->mode_paiement) }}</p>
                        <p><strong>État de livraison :</strong> {{ $commande->statut_paiement }}</p>
                        <p><strong>Mode de livraison :</strong>
                            {{ $commande->modeLivraison?->type_livraison ?? '—' }}
                        </p>
                        <p><strong>Adresse de livraison :</strong>
                            @if($commande->adresse)
                                {{ $commande->adresse->ville_adresse }} — {{ $commande->adresse->code_postal }}
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    <!-- RÉCAP COMMANDE -->
                    <div class="commande-details">
                        <h3>Articles achetés</h3>

                        <ul class="articles-list">
                            @foreach($commande->lignes as $ligne)
                                <li>
                                    <span>{{ $ligne->produit->label_produit }}</span>
                                    <span>x{{ $ligne->quantitee }}</span>
                                    <span>{{ number_format($ligne->produit->prix_base, 2) }} €</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            @endforeach

        </div>
    @endif

    </div>
    
    @include('botman')
    <script src="js/mes_commandes.js"></script>
</body>
</html>
