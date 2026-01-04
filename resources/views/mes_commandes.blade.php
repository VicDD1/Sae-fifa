<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes commandes</title>

    <link rel="stylesheet" href="{{ asset('css/mes_commandes.css') }}">
</head>

<body>

<div class="commande-container">
 <header>
        <nav>
            <a href="/">Accueil</a>
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
                
            @endauth
            @auth
                <a href="{{ route('commande.liste') }}" class="btn btn-primary">
                    Mes commandes
                </a>
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
         @endif
    </header>
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

<script>
    document.querySelectorAll('.toggle-commande').forEach(header => {
        header.addEventListener('click', () => {
            const card = header.closest('.commande-card');
            const details = card.querySelector('.commande-details');
            const icon = header.querySelector('.toggle-icon');

            const open = details.style.display === 'block';

            details.style.display = open ? 'none' : 'block';
            icon.textContent = open ? '+' : '−';
        });
    });
</script>
@include('botman')
</body>
</html>
