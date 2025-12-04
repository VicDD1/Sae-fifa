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
                <h2 class="login-title">Creer un compte</h2>
                <h3> Etape 2/2 </h3>
                <form method="POST" action="{{ route('register.step2.post') }}">
    @csrf

                    <div  class="warn">
                        <label class="input-label">Choisir son mot de passe</label>
                        <input type="password"  name="password_user_connecte" placeholder="••••••••" class="custom-input" value="{{ old('password_user_connecte') }}" required>
                        <i class="fa-regular fa-eye-slash password-icon"></i>
                        @error('password_user_connecte')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                        
                    </div>
                    
                    
                    <div class="form-group">
                        <label class="input-label">Confirmer votre mot de passe</label>
                        <input type="password" name="password_user_connecte_confirmation" value="{{ old('password_user_connecte_confirmation') }}"placeholder="••••••••" class="custom-input" required>
                        <i class="fa-regular fa-eye-slash password-icon"></i>
                    </div>
                    <div>
                        <label class="input-label">J'ai lu et j'accepte les <a href="/privacy_policy">conditions d'utilisation</a></label>
                        <input type="checkbox" name="conditions" class="custom-input" value="{{old('conditions')}}" required>
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
