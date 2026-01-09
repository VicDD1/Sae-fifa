<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil | FIFA ID</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/account_creation.css') }}">
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
                <h1>Modification.</h1>
                <p>Mettez à jour vos informations. Si vous changez votre email, vous devrez l'utiliser pour votre prochaine connexion.</p>
            </div>
            <div style="margin-top: auto;">

            </div>
        </div>

        <div class="right-panel">
            <div class="login-box" style="max-width: 550px;">
                <h2 class="login-title">Modifier mes infos</h2>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf 
                    

                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Prénom</label>
                            <input type="text" name="prenom_user_connecte" class="custom-input" value="{{ old('prenom_user_connecte', $user->prenom_user_connecte) }}" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Surnom</label>
                            <input type="text" name="surnom_user_connecte" class="custom-input" value="{{ old('surnom_user_connecte', $user->surnom_user_connecte) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">Adresse électronique</label>
                        <input type="email" name="courriel_user_connecte" class="custom-input" value="{{ old('courriel_user_connecte', $user->courriel_user_connecte) }}" required>
                        @error('courriel_user_connecte')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    </div>

                    <div style="display: flex; gap: 20px;">
                        
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Date de naissance</label>
                            <input type="date" name="date_de_naissance_user_connecte" class="custom-input" 
                                value="{{ old('date_de_naissance_user_connecte', $user->date_de_naissance_user_connecte) }}">
                                @error('date_de_naissance_user_connecte')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Pays de naissance</label>
                            <select name="pays_de_naissance_user_connecte" class="custom-input">
                                @php $pays = old('pays_de_naissance_user_connecte', $user->pays_de_naissance_user_connecte); @endphp
                                
                                <option value="France" @selected($pays == 'France')>France</option>
                                <option value="Royaume-unis" @selected($pays == 'Royaume-unis')>Royaume-Unis</option>
                                <option value="Allemagne" @selected($pays == 'Allemagne')>Allemagne</option>
                                <option value="Italie" @selected($pays == 'Italie')>Italie</option>
                                <option value="Espagne" @selected($pays == 'Espagne')>Espagne</option>
                                <option value="Portugal" @selected($pays == 'Portugal')>Portugal</option>
                            </select>

                        </div>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        
                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Langue</label>
                            <select name="langue_user_connecte" class="custom-input">
                                @php $lang = old('langue_user_connecte', $user->langue_user_connecte); @endphp

                                <option value="francais" @selected($lang == 'francais')>français</option>
                                <option value="anglais" @selected($lang == 'anglais')>anglais</option>
                                <option value="allemand" @selected($lang == 'allemand')>allemand</option>
                                <option value="italien" @selected($lang == 'italien')>italien</option>
                                <option value="espagnol" @selected($lang == 'espagnol')>espagnol</option>
                                <option value="portugais" @selected($lang == 'portugais')>portugais</option>
                            </select>
                        </div>

                        <div class="form-group" style="flex: 1;">
                            <label class="input-label">Équipe favorite</label>
                            <select name="favori_user_connecte" class="custom-input">
                                @php $team = old('favori_user_connecte', $user->favori_user_connecte); @endphp

                                <option value="francaise" @selected($team == 'francaise')>française</option>
                                <option value="anglaise" @selected($team == 'anglaise')>anglaise</option>
                                <option value="allemande" @selected($team == 'allemande')>allemande</option>
                                <option value="italienne" @selected($team == 'italienne')>italienne</option>
                                <option value="espagnole" @selected($team == 'espagnole')>espagnole</option>
                                <option value="portugaise" @selected($team == 'portugaise')>portugaise</option>
                            </select>
                        </div>
                    </div>

                
                    <button type="submit" class="btn-login" style="background-color: #00ff87; color: #001638;">
                        ENREGISTRER LES MODIFICATIONS
                    </button>

                </form>
                <div style="border-top: 2px solid #f3f4f6; padding-top: 25px;">
                    <h2 class="login-title" style="font-size: 1.2rem; margin-bottom: 15px;">Sécurité (Double Authentification)</h2>

                    @if($user->mfa_active)
                        {{-- SI ACTIVÉ : Affiche un message vert --}}
                        <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 15px; border-radius: 8px;">
                            <p style="color: #065f46; font-weight: bold; margin-bottom: 5px;">
                                <i class="fa-solid fa-shield-halved"></i> Protection activée
                            </p>
                            <p style="color: #047857; font-size: 13px;">
                                Numéro associé : <strong>{{ $user->numero_telephone_user_connecte }}</strong>
                            </p>
                        </div>
                    @else
                        {{-- SI DÉSACTIVÉ : Affiche le formulaire pour activer --}}
                        <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
                            Sécurisez votre compte avec un code SMS à chaque connexion.
                        </p>
                        
                        
                        <form action="{{ route('mfa.enable') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="input-label">Numéro de téléphone mobile</label>
                                
                                {{-- Conteneur Flexbox pour aligner Select + Input + Bouton --}}
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    
                                    {{-- 1. Choix du pays --}}
                                    <div style="width: 110px;">
                                        <select name="indicatif" class="custom-input" style="padding: 10px; border-bottom: 2px solid #ccc; font-weight: bold;">
                                            <option value="+33">🇫🇷 +33</option>
                                            <option value="+41">🇨🇭 +41</option>
                                            <option value="+32">🇧🇪 +32</option>
                                            <option value="+1">🇨🇦 +1</option>
                                            <option value="+44">🇬🇧 +44</option>
                                            <option value="+49">🇩🇪 +49</option>
                                            <option value="+34">🇪🇸 +34</option>
                                            <option value="+351">🇵🇹 +351</option>
                                            <option value="+39">🇮🇹 +39</option>
                                            <option value="+212">🇲🇦 +212</option>
                                            <option value="+213">🇩🇿 +213</option>
                                            <option value="+216">🇹🇳 +216</option>
                                        </select>
                                    </div>

                                    {{-- 2. Saisie du numéro --}}
                                    <div style="flex: 1; display: flex; flex-direction: column;">
                                        <input type="text" 
                                            name="numero_suffixe" 
                                            class="custom-input" 
                                            placeholder="6 12 34 56 78" 
                                            inputmode="numeric"
                                            maxlength="10"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            required>
                                        
                                        {{-- Message d'erreur --}}
                                        @error('numero_suffixe')
                                            <div class="error-message" style="color: #d7003a; margin-top: 5px; font-size: 11px;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- 3. Bouton OK --}}
                                    <button type="submit" 
                                            class="btn-login" 
                                            style="width: auto; padding: 12px 20px; background-color: #3b82f6; color: white; margin-top: 0; cursor: pointer; position: relative; z-index: 10;">
                                        Activer
                                    </button>
                                </div>

                                <p style="font-size: 11px; color: #888; margin-top: 8px;">
                                    <i class="fa-solid fa-circle-info"></i> Sélectionnez votre pays, puis tapez votre numéro (avec ou sans le 0).
                                </p>
                            </div>
                        </form>
                        
                    @endif
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>