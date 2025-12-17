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

    <div class="container">
        <div class="left-panel">
            <div class="fifa-logo">FIFA ID</div>
            
            <div class="hero-text">
                <h1>Le football au bout des doigts.</h1>
                <p>Inscrivez-vous pour accéder à la billetterie, jouer à des jeux et suivre les qualifications pour la Coupe du Monde de la FIFA 2026™!</p>
            </div>
            <div></div> </div>

        <div class="right-panel">
        <div class="login-box">
                <h2 class="login-title">Se connecter</h2>
                @if(session('success'))
                    <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 4px; margin-bottom: 20px; text-align: center; border: 1px solid #a7f3d0; font-size: 14px;">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <form action="/connexion" method="POST">
                    
                    @csrf 

                    <div class="form-group">
                        <label class="input-label">Adresse électronique</label>
                        <input type="email" name="email" class="custom-input" value="{{ old('email') }}" required>
                        
                        @if($errors->has('email'))
                            <p style="color: #d7003a; font-size: 13px; margin-top: 5px;">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('email') }}
                            </p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="input-label">Mot de passe</label>
                        <input type="password" name="password" class="custom-input" required>
                        <i class="fa-regular fa-eye-slash password-icon"></i>
                    </div>

                    <button type="submit" class="btn-login">SE CONNECTER</button>
                    <div style="margin-top: 15px; text-align: center;">
                    <a href="{{ route('password.request') }}" style="color: #d9534f; text-decoration: underline;">
                        Réinitialiser mon mot de passe 
                    </a>
</div>
                </form>

                <div class="signup-area">
                    <p class="signup-text">Vous n'avez pas de compte ?</p>
                    <a href="/creer_un_compte_1"> <button class="btn-signup">S'INSCRIRE</button> </a>
                </div>
                
                <div class="signup-area">
                    <p class="signup-text">Souhaitez vous creer un compte professionnel ?</p>
                    <a href="/creer_un_compte_professionnel_1"> <button class="btn-signup">CREER</button> </a>
                </div>

                <footer class="footer">
                    <a href="/privacy_policy"> Conditions d'utilisation </a>
                    <span>|</span>
                    <a href="/privacy_policy"> Respect de la vie privée </a> 
                </footer>
    
            </div>
    </div>

</body>
</html>