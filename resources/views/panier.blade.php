<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon Panier - FIFA Store</title>
<link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/account_panier.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

</head>

<body>

    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex;  width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>


            <!-- CORRECTION : lien Vote propre -->
            <a href="{{ route('vote.page') }}">Vote</a>

            
            <a href="https://www.fifa.com/fr/news" target="_blank">L'Actu </a>

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



<main class="cart-container">

    <div class="cart-items">
        <h1>Mon Panier</h1>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif


        @if($lignes->count() >= 0)

            @foreach($lignes as $ligne)
                <div class="cart-item">

                    <img 
                        src="../assets/photo_produit/{{ $ligne->id_produit }}.webp"
                        alt="photo {{ $ligne->produit->label_produit }}"
                        style="width:120px; height:auto; border-radius:8px;"
                    >

                    <div class="item-details">
                        
                        <h3>{{ $ligne->produit->label_produit }}</h3>

                        <div class="item-options" style="font-size: 14px; color: #666; margin-bottom: 5px;">
                            Taille :
                            <strong>{{ $ligne->taille->label_taille ?? 'Standard' }}</strong> |
                            Couleur :
                            <strong>{{ $ligne->couleur->label_colori ?? 'Unique' }}</strong>
                        </div>

                        <div class="quantity-controls">
                            <span style="margin-right: 10px; color: #666;">Quantité :</span>

                            <a href="{{ route('panier.update', ['id_ligne' => $ligne->id_ligne, 'action' => 'minus']) }}"
                               class="qty-btn">
                                <i class="fa-solid fa-minus"></i>
                            </a>

                            <span class="qty-number">{{ $ligne->quantitee }}</span>

                            <a href="{{ route('panier.update', ['id_ligne' => $ligne->id_ligne, 'action' => 'plus']) }}"
                               class="qty-btn">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                        </div>

                        <div class="item-price">
                            {{ number_format($ligne->quantitee * $ligne->produit->prix_base, 2) }} €
                        </div>

                        <a href="{{ route('panier.supprimer', $ligne->id_ligne) }}" class="btn-delete">
                            <i class="fa-solid fa-trash"></i> Supprimer
                        </a>

                    </div>
                </div>
            @endforeach

        @else
            <div class="empty-cart">
                <h2>Votre panier est vide.</h2>
                <p>Vous n'avez pas encore ajouté d'articles.</p>

                <a href="{{ url('/produits') }}" class="btn-checkout" style="display:inline-block; width:auto; margin-top:20px;">
                    Retourner à la boutique
                </a>
            </div>
        @endif
    </div>


    @if($lignes->count() > 0)
        <div class="cart-summary">

            <h2 style="font-size: 18px; margin-top: 0;">Récapitulatif</h2>

            @php
                $countItems = $lignes->sum('quantitee');
            @endphp

            <p>Sous-total ({{ $countItems }} articles) :</p>

            <div class="total-price">{{ number_format($total, 2) }} €</div>

            <a href="/commande" class="btn-checkout">Passer la commande</a>
        </div>
    @endif

</main>
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
