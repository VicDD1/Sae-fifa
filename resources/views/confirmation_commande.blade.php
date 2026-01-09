<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement de la commande</title>
<link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/confirmation_commande.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>

    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex;  width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>


            <!-- CORRECTION : lien Vote propre -->
            <a href="{{ route('vote.page') }}">Vote</a>

            
            <a href="/blog">L'Actu </a>

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
                        <img style="text-decoration: none; display: flex; align-items: center; width:40px;" src="{{asset('assets/iconEdit.png')}}" alt="voir mes informations"></img>
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
            <div class="nav-right-group">
                <a style="margin-left: auto;" class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>
                 <a style="margin-left: auto;" class="account_creation" href="/localisation_des_ventes"><img src="{{ asset('assets/map.png') }}" alt="Compte"></a>

            </div>
            @endif

                
            @endauth
            @auth
                @if (!Auth::user()->professionnel && Auth::user()->id_user_connecte !== 11 && Auth::user()->id_user_connecte !== 13)
                <a href="{{ route('commande.liste') }}" class="btn btn-primary">
                    Mes commandes
                </a>
                @endif
            @endauth

            @auth
                @if (!Auth::user()->professionnel && Auth::user()->id_user_connecte !== 11 && Auth::user()->id_user_connecte !== 13)
                    <a href="/creer_un_compte_professionnel_1" class="account_creation">
                        <p>Compte professionnel</p>
                    </a>
                @endif

                @if ((Auth::user()->id_user_connecte !== 12 || Auth::user()->id_user_connecte !== 11) && Auth::user()->professionnel)
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

            @if (session('error'))
                <p>{{ session('error') }}</p>
            @endif

            @if ($errors->any())
                <div>
                    <p>Erreurs :</p>
                    <ul>
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

        <form action="{{ route('commande.payer') }}" method="POST" class="payment-form">
            @csrf
            <input type="hidden" name="nom" value="{{ $data['nom'] }}">
            <input type="hidden" name="adresse" value="{{ $data['adresse'] }}">
            <input type="hidden" name="ville" value="{{ $data['ville'] }}">
            <input type="hidden" name="cp" value="{{ $data['cp'] }}">
            <input type="hidden" name="telephone" value="{{ $data['telephone'] }}">
            <input type="hidden" name="paiement" value="{{ $data['paiement'] }}">
            <input type="hidden" name="mode_livraison" value="{{ $mode->id_mode_livraison }}">
            @if(isset($cartes) && $cartes->count() > 0)
                <div class="section-title">Carte enregistrée</div>

                <div class="info-box">
                    @foreach($cartes as $index => $carte)
                        <label style="display:block; margin-bottom:8px;">
                            <input
                                type="radio"
                                name="carte_existante"
                                value="{{ $carte->id_carte }}"
                                data-nom="{{ $carte->nom_titulaire }}"
                                data-expiry="{{ Crypt::decryptString($carte->date_expiration) }}"
                            >
                            Carte {{ $index + 1 }} — {{ $carte->nom_titulaire }}
                            (expire {{ Crypt::decryptString($carte->date_expiration) }})
                        </label>
                    @endforeach

                    <label style="display:block; margin-top:10px;">
                        <input type="radio" name="carte_existante" value="nouvelle" checked>
                        Utiliser une nouvelle carte
                    </label>
                </div>
            @endif


            <label>Titulaire de la carte</label>
            <input type="text" name="card_name" id="card_name"
                required
                minlength="2"
                maxlength="60"
                pattern="^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$"
                title="Nom invalide. Caractères autorisés : lettres, espaces, tirets.">

            <label>Numéro de carte bancaire</label>
            <input type="text" name="card_number" id="card_number"
                required
                pattern="^[0-9]{16}$"
                maxlength="16"
                inputmode="numeric"
                title="Le numéro doit contenir 16 chiffres.">

            <div class="row">
                <div class="col">
                    <label>Date d’expiration</label>
                    <input type="text" name="expiry" id="expiry"
                        required
                        pattern="^(0[1-9]|1[0-2])\/([0-9]{2})$"
                        maxlength="5"
                        placeholder="MM/AA"
                        title="Format requis : MM/AA.">
                </div>

                <div class="col">
                    <label>CVV</label>
                    <input type="text" name="cvv" id="cvv"
                        required
                        pattern="^[0-9]{3}$"
                        maxlength="3"
                        title="Le code CVV doit contenir 3 chiffres.">
                </div>
            </div>

            <button type="submit" class="btn-pay">Confirmer et payer</button>
        </form>

        </div>
    </div>


<script>
document.querySelectorAll('input[name="carte_existante"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const isNouvelle = this.value === 'nouvelle';

        const nameInput   = document.getElementById('card_name');
        const numberInput = document.getElementById('card_number');
        const expiryInput = document.getElementById('expiry');
        const cvvInput    = document.getElementById('cvv');

        if (!isNouvelle) {
            // Existing card selected
            if (nameInput) {
                nameInput.value = this.dataset.nom || '';
            }
            numberInput.value = '';
            cvvInput.value = '';

            numberInput.readOnly = true;
            cvvInput.readOnly = true;
            numberInput.disabled = false;
            cvvInput.disabled = false;

            numberInput.placeholder = "**** **** **** ****";
            cvvInput.placeholder = "***";

            // Remove required attribute when using existing card
            numberInput.removeAttribute('required');
            cvvInput.removeAttribute('required');
            if (expiryInput) expiryInput.removeAttribute('required');
        } else {
            // New card selected
            numberInput.readOnly = false;
            cvvInput.readOnly = false;
            numberInput.disabled = false;
            cvvInput.disabled = false;

            numberInput.placeholder = "";
            cvvInput.placeholder = "";

            // Add back required attribute for new card
            numberInput.setAttribute('required', 'required');
            cvvInput.setAttribute('required', 'required');
            if (expiryInput) expiryInput.setAttribute('required', 'required');
        }

    });
});

// Apply initial state on page load (useful after validation errors)
document.addEventListener('DOMContentLoaded', () => {
    const selected = document.querySelector('input[name="carte_existante"]:checked');
    if (selected) selected.dispatchEvent(new Event('change'));
});
</script>

    <footer>
    <a href="{{ route('cookies.manage') }}">Gérer mes cookies</a>
        <span>|</span>
    <a href="/privacy_policy"> Conditions d'utilisation </a>
        <span>|</span>
     <a href="/privacy_policy"> Respect de la vie privée </a> 
</footer>
    @include('botman')
</body>
</html>
