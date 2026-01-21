<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | FIFA ID</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/account_creation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>
<body>
@include('header')
    <div class="container">
        <div class="left-panel">
            <div class="fifa-logo">FIFA ID</div>
            <div class="hero-text">
                <h1>Mon Espace Personnel</h1>
                <p>Retrouvez ici vos informations personnelles.</p>
            </div>
            <div style="margin-top: auto;">
                
            </div>
        </div>

        <div class="right-panel">
            <div class="login-box" style="max-width: 500px;">
                <h2 class="login-title">Mes Informations</h2>

                <form>
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Prénom</label>
                            <input type="text" class="custom-input" value="{{ $user->prenom_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Surnom</label>
                            <input type="text" class="custom-input" value="{{ $user->surnom_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">Email</label>
                        <input type="email" class="custom-input" value="{{ $user->courriel_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Date de naissance</label>
                            <input type="text" class="custom-input" value="{{ $user->date_de_naissance_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Pays</label>
                            <input type="text" class="custom-input" value="{{ $user->pays_de_naissance_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Langue</label>
                            <input type="text" class="custom-input" value="{{ $user->langue_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Équipe Favorite</label>
                            <input type="text" class="custom-input" value="{{ $user->favori_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">Numéro de téléphone</label>
                        <input type="text" class="custom-input" value="{{ $user->numero_telephone_user_connecte }}" readonly style="background-color: #f9f9f9; color: #555;">
                    </div>

                    <div class="form-group">
                        <label class="input-label">Mot de passe</label>
                        <div style="position: relative;">
                            <input type="password" class="custom-input" value="FakePassword123" readonly style="background-color: #f9f9f9; color: #555;">
                            <i class="fa-solid fa-lock password-icon"></i>
                        </div>
                    </div>

                    

                    <a href="/parametre_compte" style="text-decoration: none;">
                        <button type="button" class="btn-login" style="background-color: #045694; cursor: pointer;">MODIFIER MES INFOS</button>
                    </a>
                    <a href="/anonymize" style="text-decoration: none;">
                        <button type="button" class="btn-anonyme" style="background-color: #ff8000;cursor:pointer;">ANONYMISER MES INFOS</button>
                    </a>
                </form>
                <div id="rgpd">
                    <a href="/delete" style="text-decoration: none;">
                        <button type="button" class="btn-suprimer" style="background-color: #ff0000;cursor:pointer;">SUPPRIMER MES INFOS</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @include('botman')
</body>
</html>