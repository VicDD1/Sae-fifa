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
</head>
<body>
@include('header')

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
                        <input type="text" name="prenom_user_connecte" value="{{ old('prenom_user_connecte') }}" class="custom-input" required>
                        <label class="input-label">Prenom</label>
                    </div>



                    <div >
                        <input type="email" name="courriel_user_connecte" value="{{ old('courriel_user_connecte') }}" class="custom-input" required>
                        <label class="input-label">Adresse électronique</label>
                    </div>
                    @error('courriel_user_connecte')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
 

                    <div>
                        <input type="text" name="surnom_user_connecte" value="{{ old('surnom_user_connecte') }}" class="custom-input">
                        <label class="input-label">pseudonyme</label>
                    </div>
                    <div>
                        <input type="date" name="date_de_naissance_user_connecte" value="{{ old('date_de_naissance_user_connecte') }}"  class="custom-input" required>
                        <label class="input-label">Date de naissance</label>
                    </div>
                    @error('date_de_naissance_user_connecte')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="select">
                        
                        <select name="pays_de_naissance_user_connecte"  value="{{ old('pays_de_naissance_user_connecte') }}">
                            
                            <option value="France">France</option>
                        <option value="Royaume-unis">Royaume-unis</option>
                        <option value="Allemagne">Allemagne</option>
                        <option value="Italie">Italie</option>
                        <option value="Espagne">Espagne</option>
                        <option value="Portugal">Portugal</option>
                    </select>
                    <label class="input-label">Pays de naissance</label>
                    </div>

                    <div class="select">
                        
                        <select name="favori_user_connecte" value="{{ old('favori_user_connecte') }}">

                        <option value="francaise">francaise</option>
                        <option value="anglaise">anglaise</option>
                        <option value="allemande">allemande</option>
                        <option value="italienne">italienne</option>
                        <option value="espagnole">espagnole</option>
                        <option value="portugaise">portugaise</option>
                        </select>
                        <label class="input-label">Equipe favorite</label>
                    </div>

                    <div class="select">
                        
                        <select name="langue_user_connecte" value="{{ old('langue_user_connecte') }}">
                            
                            <option value="francais">francais</option>
                        <option value="anglais">anglais</option>
                        <option value="allemand">allemand</option>
                        <option value="italien">italien</option>
                        <option value="espagnol">espagnol</option>
                        <option value="portugais">portugais</option>
                        </select>
                        <label class="input-label">Langue</label>
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