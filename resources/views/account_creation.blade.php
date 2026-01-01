<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | FIFA ID</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/account_creation.css">
</head>
<body>
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

    <div class="container">
        <div class="left-panel">
            <div class="fifa-logo">FIFA ID</div>
            @if(session('error'))
    <div class='erreur'>
        {{session('error')}}
    </div>
@endif
            <div class="hero-text">
                <h1>Le football au bout des doigts.</h1>
                <p>Inscrivez-vous pour accéder à la billetterie, jouer à des jeux et suivre les qualifications pour la Coupe du Monde de la FIFA 2026™!</p>
            </div>
            <div></div> </div>

        <div class="right-panel">

            <div class="login-box">
                <h2 class="login-title">Creer un compte</h2>
                <h1> Etape 1/2 <h1>
                <form method="POST" action="{{ route('register.step1.post') }}">
    @csrf



                    <div>
                        <label class="input-label">Prenom</label>
                        <input type="text" name="prenom_user_connecte" value="{{ old('prenom_user_connecte') }}" class="custom-input" required>
                    </div>



                    <div >
                        <label class="input-label">Adresse électronique</label>
                        <input type="email" name="courriel_user_connecte" value="{{ old('courriel_user_connecte') }}" class="custom-input" required>
                    </div>
                    @error('courriel_user_connecte')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
 

                    <div>
                        <label class="input-label">pseudonyme</label>
                        <input type="text" name="surnom_user_connecte" value="{{ old('surnom_user_connecte') }}" class="custom-input">
                    </div>
                    <div>
                        <label class="input-label">Date de naissance</label>
                        <input type="date" name="date_de_naissance_user_connecte" value="{{ old('date_de_naissance_user_connecte') }}"  class="custom-input" required>
                    </div>
                    @error('date_de_naissance_user_connecte')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="select">
                        <label class="input-label">Pays de naissance</label>

                        <select name="pays_de_naissance_user_connecte"  value="{{ old('pays_de_naissance_user_connecte') }}">

                        <option value="France">France</option>
                        <option value="Royaume-unis">Royaume-unis</option>
                        <option value="Allemagne">Allemagne</option>
                        <option value="Italie">Italie</option>
                        <option value="Espagne">Espagne</option>
                        <option value="Portugal">Portugal</option>
                        </select>
                    </div>

                    <div class="select">
                        <label class="input-label">Equipe favorite</label>

                        <select name="favori_user_connecte" value="{{ old('favori_user_connecte') }}">

                        <option value="francaise">francaise</option>
                        <option value="anglaise">anglaise</option>
                        <option value="allemande">allemande</option>
                        <option value="italienne">italienne</option>
                        <option value="espagnole">espagnole</option>
                        <option value="portugaise">portugaise</option>
                        </select>
                    </div>

                    <div class="select">
                        <label class="input-label">Langue</label>

                        <select name="langue_user_connecte" value="{{ old('langue_user_connecte') }}">

                        <option value="francais">francais</option>
                        <option value="anglais">anglais</option>
                        <option value="allemand">allemand</option>
                        <option value="italien">italien</option>
                        <option value="espagnol">espagnol</option>
                        <option value="portugais">portugais</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-login">POURSUIVRE</button> 
                </form>

               



            
                




                <footer class="footer">
                    <a href="#"> Conditions d'utilisation</a>
                    <span>|</span>
                    <a href="/privacy_policy"> Respect de la vie privée </a> </footer>

            </div>
        </div>
    </div>
   @include('botman')
</body>
</html>