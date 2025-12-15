<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement de la commande</title>

    <link rel="stylesheet" href="{{ asset('css/confirmation_commande.css') }}">
</head>

<body>

    <header>
        <nav>
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
    </header>
    <div class="container">
        <div class="card">

            <h1>Paiement de votre commande</h1>
            <p class="intro">
                Merci pour votre commande.  
                Veuillez renseigner vos informations bancaires pour finaliser l’achat.
            </p>

        <!-- INFORMATIONS CLIENT -->
        <div class="section-title">Informations de livraison</div>
        <div class="info-box">
            <p><strong>Nom :</strong> {{ $data['nom'] }}</p>
            <p><strong>Adresse :</strong> {{ $data['adresse'] }}</p>
            <p><strong>Ville :</strong> {{ $data['ville'] }} — {{ $data['cp'] }}</p>
            <p><strong>Téléphone :</strong> {{ $data['telephone'] }}</p>
        </div>

        <!-- MODE DE LIVRAISON -->
        <div class="section-title">Mode de livraison</div>
        <div class="info-box">
            <p><strong>Type :</strong> {{ $mode->type_livraison }}</p>
            <p><strong>Coût :</strong> {{ number_format($mode->prix_mode_livraison, 2, ',', ' ') }} €</p>
        </div>

        <!-- MODE DE PAIEMENT -->
        <div class="section-title">Mode de paiement</div>
        <p><strong>{{ ucfirst($data['paiement']) }}</strong></p>

        <!-- RÉCAPITULATIF TOTAL -->
        <div class="section-title">Montant total</div>

        <div class="total-block">
            <div class="total-row">
                <span>Total articles :</span>
                <strong>{{ number_format($total - $mode->prix_mode_livraison, 2, ',', ' ') }} €</strong>
            </div>

            <div class="total-row">
                <span>Livraison :</span>
                <strong>{{ number_format($mode->prix_mode_livraison, 2, ',', ' ') }} €</strong>
            </div>

            <div class="total-final">
                Total final : {{ number_format($total, 2, ',', ' ') }} €
            </div>
        </div>

        <!-- CONTENU PANIER -->
        <div class="section-title">Votre panier</div>
        <ul>
            @foreach($lignes as $ligne)
                <li>
                    {{ $ligne->produit->label_produit }}
                    (x{{ $ligne->quantitee }}) —
                    {{ number_format($ligne->produit->prix_base, 2, ',', ' ') }} €
                </li>
            @endforeach
        </ul>

        <form action="{{ route('commande.succes') }}" method="POST" class="payment-form">
            @csrf
            <input type="hidden" name="nom" value="{{ $data['nom'] }}">
            <input type="hidden" name="adresse" value="{{ $data['adresse'] }}">
            <input type="hidden" name="ville" value="{{ $data['ville'] }}">
            <input type="hidden" name="cp" value="{{ $data['cp'] }}">
            <input type="hidden" name="telephone" value="{{ $data['telephone'] }}">
            <input type="hidden" name="paiement" value="{{ $data['paiement'] }}">
            <input type="hidden" name="mode_livraison" value="{{ $mode->id_mode_livraison }}">

            <label>Titulaire de la carte</label>
            <input type="text" name="card_name"
                required
                minlength="2"
                maxlength="60"
                pattern="^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$"
                title="Nom invalide. Caractères autorisés : lettres, espaces, tirets.">

            <label>Numéro de carte bancaire</label>
            <input type="text" name="card_number"
                required
                pattern="^[0-9]{16}$"
                maxlength="16"
                inputmode="numeric"
                title="Le numéro doit contenir 16 chiffres.">

            <div class="row">
                <div class="col">
                    <label>Date d’expiration</label>
                    <input type="text" name="expiry"
                        required
                        pattern="^(0[1-9]|1[0-2])\/([0-9]{2})$"
                        maxlength="5"
                        placeholder="MM/AA"
                        title="Format requis : MM/AA.">
                </div>

                <div class="col">
                    <label>CVV</label>
                    <input type="text" name="cvv"
                        required
                        pattern="^[0-9]{3}$"
                        maxlength="3"
                        title="Le code CVV doit contenir 3 chiffres.">
                </div>


            <button type="submit" class="btn-pay">Confirmer et payer</button>
        </form>

            <a href="{{ url('/produits') }}" class="return">Retour à la boutique</a>
        </div>
    </div>

</body>
</html>
