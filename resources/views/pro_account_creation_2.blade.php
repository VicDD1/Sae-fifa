<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | FIFA ID</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/account_creation.css">
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
        <div class="left-panel">
            <div class="fifa-logo">FIFA ID</div>
            
            <div class="hero-text">

            </div>
            <div></div> </div>

        <div class="right-panel">
            <div class="login-box">
                <h2 class="login-title">Creer un compte professionnel</h2>
                <h3> Etape 2/2 </h3>
                <form method="POST" action="{{ route('registerPro.step2.post') }}">
    @csrf

                    <div class="form-group">
                        <label class="input-label">Choisir son mot de passe</label>
                        <input type="password"  name="password_professionnel" placeholder="••••••••" value="{{ old('password_professionnel') }}" class="custom-input" required>
                        <i class="fa-regular fa-eye-slash password-icon"></i>
                    </div>
                    @error('password_professionnel')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-group">
                        <label class="input-label">Confirmer votre mot de passe</label>
                        <input type="password" name="password_professionnel_confirmation" placeholder="••••••••" class="custom-input" value="{{ old('password_professionnel_confirmation') }}" required>
                        <i class="fa-regular fa-eye-slash password-icon"></i>
                    </div>
                    <div>
                        <label class="input-label">J'ai lu et j'accepte les <a href="/privacy_policy">conditions d'utilisation</a></label>
                        <input type="checkbox" name="conditions" class="custom-input" required>
                    </div>

                    <button type="submit" class="btn-login">créer le compte</button>
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
<script>
    document.querySelectorAll('.password-icon').forEach((icon) => {
        icon.addEventListener('click', () => {
            const input = icon.previousElementSibling; 

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
                else {
                input.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        });
    });
</script>