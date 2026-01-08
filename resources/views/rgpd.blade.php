<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/rgpd.css') }}">
    <title>Gestion RGPD - FIFA Store</title>
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
                <a class="account_creation" href="/localisation_des_ventes"><img src="{{ asset('assets/map.png') }}" alt="Compte"></a>

            <?php
            var_dump(Auth::user()->id_user_connecte)
            ?>    
            @endif
            @if (Auth::user()->id_user_connecte === 35)
                <a class="account_creation" href="/gestion-rgpd">Espace DPO </a>
            @endif
                
            @endauth
            @auth
                <a href="{{ route('commande.liste') }}" class="btn btn-primary">
                    Mes commandes
                </a>
            @endauth

            @auth
                @if (!Auth::user()->professionnel && Auth::user()->id_user_connecte !== 11 && Auth::user()->id_user_connecte !== 13)
                    <a href="/creer_un_compte_professionnel_1" class="account_creation">
                        <p>Compte professionnel</p>
                    </a>
                @endif

                @if (Auth::user()->id_user_connecte !== 12 || Auth::user()->id_user_connecte !== 11)
                    <a href="/proposer_un_produit" class="account_creation">
                        <p>faire une demande de produit</p>
                    </a>
                @endif
            @endauth
        </nav>
    </header>
    <div class="rgpd-container" style="max-width: 1000px; margin: auto; padding: 20px;">
    <h1>Gestion des données (DPO)</h1>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="rgpd-card" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
        <form action="{{ route('rgpd.gestion') }}" method="GET">
            <label><strong>Afficher les comptes qui se sont connecter avant le :</strong></label>
            <input type="date" name="date_limite" value="{{ $dateChoisie ?? '' }}" required>
            <button type="submit" class="btn-primary">Filtrer les comptes</button>
        </form>
    </div>

    @if(isset($users))
        <form action="{{ route('rgpd.anonymize') }}" method="POST" style="margin-top: 30px;">
            @csrf
            <h3>Résultats du filtre : {{ $users->count() }} compte(s) trouvé(s)</h3>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background: #2c3e50; color: white;">
                        <th style="padding: 10px;">Suppr.</th>
                        <th style="padding: 10px;">ID</th>
                        <th style="padding: 10px;">Pseudo / Prénom</th>
                        <th style="padding: 10px;">Email</th>
                        <th style="padding: 10px;">Denière Connexion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="text-align: center;"><input type="checkbox" name="user_ids[]" value="{{ $user->id_user_connecte }}"></td>
                            <td style="padding: 10px;">{{ $user->id_user_connecte }}</td>
                            <td style="padding: 10px;">{{ $user->surnom_user_connecte }} {{ $user->prenom_user_connecte }}</td>
                            <td style="padding: 10px;">{{ $user->courriel_user_connecte }}</td>
                            <td style="padding: 10px;">{{ $user->created_at ? date('d/m/Y', strtotime($user->created_at)) : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="padding: 20px; text-align: center;">Aucun utilisateur trouvé pour cette date.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if($users->count() > 0)
                <button type="submit" class="btn-anonymize" style="background: #090707ff; color: white; border: none; padding: 10px 20px; cursor: pointer;"
                        onclick="return confirm('Anonymiser les comptes sélectionnés ?')">
                    Anonymiser la sélection
                </button>
            @endif
        </form>
    @endif
</div>
</body>
</html>