<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes commandes</title>

    <link rel="stylesheet" href="{{ asset('css/mes_commandes.css') }}">
</head>

<body>
    <header>
        <nav>
            <a href="/">Acceuil</a>
            <a href="/produits">Fifa Store</a>

            <!-- CORRECTION : lien Vote propre -->
            <a href="{{ route('vote.page') }}">Vote</a>

            <a href="/players">Les joueurs</a>
            <a href="https://www.fifa.com/fr/news" target="_blank">Les Articles</a>

            @auth
                @php
                    $panier = \App\Models\Panier::where('id_user_connecte', Auth::id())->first();
                    $totalQuantite = $panier ? $panier->lignes->sum('quantitee') : 0;
                @endphp
            @endauth

            @guest
                @php $totalQuantite = 0; @endphp
            @endguest

            <a href="{{ route('panier.index') }}" style="margin-left: 15px; font-weight: bold;">
                <i class="fa-solid fa-cart-shopping"></i> Mon Panier ({{ $totalQuantite }})
            </a>

            @auth
                <div style="display: inline-flex; align-items: center; margin-left: 20px; color: white;">
                    
                    <a href="/mon-profil" style="text-decoration: none; display: flex; align-items: center;">
                        <span style="margin-right: 10px; font-weight: bold; border-bottom: 2px solid #00ff87;">
                            {{ Auth::user()->prenom_user_connecte ?? Auth::user()->surnom_user_connecte }}
                        </span>
                    </a>

                    <form action="/logout" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" title="Se déconnecter" style="background: none; border: none; cursor: pointer; color: #ffcccc;">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </form>

                </div>
            @endauth

            @guest
                <a href="/connexion" class="account_creation" title="Se connecter">
                    <img src="{{ asset('assets/icone.png') }}" alt="Compte">
                </a>
            @endguest
            @auth

            @if (Auth::user()->id_user_connecte === 12 || Auth::user()->id_user_connecte === 11)
                <a class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>


                <a href="/proposer_un_produit"  class="account_creation"><p>faire une demande de produit</p></a>
            @endif
                
            @endauth

            @auth
                @if (!Auth::user()->professionnel)
                    <a href="/creer_un_compte_professionnel_1" class="account_creation">
                        <p>Compte professionnel</p>
                    </a>
                @endif

                @if (Auth::user()->professionnel)
                    <a href="/proposer_un_produit" class="account_creation">
                        <p>faire une demande de produit</p>
                    </a>
                @endif
            @endauth
        </nav>
    </header>
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
