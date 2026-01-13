<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Paiement de la commande</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/confirmation_commande.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <script src="https://js.stripe.com/v3/"></script>

</head>

<body>

<div class="commande-container">
    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex;  width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>
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
                <a href="{{ route('commande.liste') }}" class="btn btn-primary">Mes commandes</a>
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
                Veuillez renseigner vos informations bancaires pour finaliser l'achat.
            </p>

            @if (session('error'))
                <p style="color: red;">{{ session('error') }}</p>
            @endif

            @if ($errors->any())
                <div style="color: red;">
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

            <!-- PAIEMENT STRIPE -->
            <div class="section-title">Paiement sécurisé par Stripe</div>
            
            <!-- Cartes sauvegardées -->
            <div id="saved-cards-container" class="saved-cards" style="display: none;">
                <p><strong>Vos cartes enregistrées :</strong></p>
                <div id="saved-cards-list"></div>
                <label style="display: block; margin-top: 10px;">
                    <input type="radio" name="payment_method" value="new" checked>
                    Utiliser une nouvelle carte
                </label>
            </div>

            <!-- Formulaire Stripe -->
            <div id="new-card-form" class="stripe-form">
                <label>Informations de carte bancaire</label>
                <div id="card-element"></div>
                <div id="card-errors" role="alert"></div>
                
                <div class="save-card-checkbox">
                    <input type="checkbox" id="save-card" checked>
                    <label for="save-card">Enregistrer cette carte pour mes prochains achats</label>
                </div>
            </div>

            <button id="submit-payment" class="btn-pay">
                <span id="button-text">Payer {{ number_format($total, 2, ',', ' ') }} €</span>
                <span id="loading-spinner" class="loading-spinner">Traitement...</span>
            </button>

            <div id="payment-message" style="margin-top: 15px; display: none;"></div>
        </div>
    </div>

    <footer>
        <a href="{{ route('cookies.manage') }}">Gérer mes cookies</a>
        <span>|</span>
        <a href="/privacy_policy">Conditions d'utilisation</a>
        <span>|</span>
        <a href="/privacy_policy">Respect de la vie privée</a>
    </footer>

    @include('botman')

    <script>
        // Configuration Stripe
        const stripe = Stripe('{{ config('stripe.key') }}');
        const elements = stripe.elements();
        
        // Style pour Stripe Elements
        const style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        const cardElement = elements.create('card', { style: style });
        cardElement.mount('#card-element');

        // Afficher les erreurs de validation
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Données de la commande
        const orderData = {
            nom: '{{ $data['nom'] }}',
            adresse: '{{ $data['adresse'] }}',
            ville: '{{ $data['ville'] }}',
            cp: '{{ $data['cp'] }}',
            telephone: '{{ $data['telephone'] }}',
            mode_livraison: {{ $mode->id_mode_livraison }}
        };

        let clientSecret = null;
        let selectedPaymentMethod = null;

        // Charger les cartes sauvegardées
        async function loadSavedCards() {
            try {
                const response = await fetch('{{ route('stripe.savedCards') }}');
                const data = await response.json();
                
                if (data.cards && data.cards.length > 0) {
                    const container = document.getElementById('saved-cards-container');
                    const list = document.getElementById('saved-cards-list');
                    
                    container.style.display = 'block';
                    list.innerHTML = '';
                    
                    data.cards.forEach(card => {
                        const div = document.createElement('div');
                        div.className = 'saved-card-item';
                        div.innerHTML = `
                            <label>
                                <input type="radio" name="payment_method" value="${card.id}">
                                <span class="card-brand">${card.brand}</span> •••• ${card.last4} 
                                (expire ${card.exp_month}/${card.exp_year})
                            </label>
                        `;
                        list.appendChild(div);
                    });

                    // Gérer la sélection
                    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            selectedPaymentMethod = this.value === 'new' ? null : this.value;
                            document.getElementById('new-card-form').style.display = 
                                this.value === 'new' ? 'block' : 'none';
                        });
                    });
                }
            } catch (error) {
                console.error('Erreur chargement cartes:', error);
            }
        }

        // Créer le PaymentIntent
        async function createPaymentIntent() {
            const response = await fetch('{{ route('stripe.createPaymentIntent') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    mode_livraison: orderData.mode_livraison
                })
            });
            
            const data = await response.json();
            if (data.error) {
                throw new Error(data.error);
            }
            return data.clientSecret;
        }

        // Soumettre le paiement
        document.getElementById('submit-payment').addEventListener('click', async function() {
            const button = this;
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('loading-spinner');
            const messageDiv = document.getElementById('payment-message');

            button.disabled = true;
            buttonText.style.display = 'none';
            spinner.style.display = 'inline';
            messageDiv.style.display = 'none';

            try {
                // Paiement avec carte sauvegardée
                if (selectedPaymentMethod) {
                    const response = await fetch('{{ route('stripe.payWithSavedCard') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            payment_method_id: selectedPaymentMethod,
                            ...orderData
                        })
                    });

                    const result = await response.json();
                    if (result.success) {
                        window.location.href = result.redirect;
                    } else {
                        throw new Error(result.error || 'Erreur de paiement');
                    }
                } else {
                    // Nouveau paiement avec carte
                    if (!clientSecret) {
                        clientSecret = await createPaymentIntent();
                    }

                    const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
                        payment_method: {
                            card: cardElement,
                            billing_details: {
                                name: orderData.nom
                            }
                        }
                    });

                    if (error) {
                        throw new Error(error.message);
                    }

                    if (paymentIntent.status === 'succeeded') {
                        // Confirmer le paiement côté serveur
                        const response = await fetch('{{ route('stripe.confirmPayment') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                payment_intent_id: paymentIntent.id,
                                save_card: document.getElementById('save-card').checked,
                                ...orderData
                            })
                        });

                        const result = await response.json();
                        if (result.success) {
                            window.location.href = result.redirect;
                        } else {
                            throw new Error(result.error || 'Erreur de confirmation');
                        }
                    }
                }
            } catch (error) {
                messageDiv.textContent = error.message;
                messageDiv.style.display = 'block';
                messageDiv.style.color = 'red';
                button.disabled = false;
                buttonText.style.display = 'inline';
                spinner.style.display = 'none';
            }
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadSavedCards();
        });
    </script>
</div>
</body>
</html>
