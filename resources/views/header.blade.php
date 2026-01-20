<header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex;  width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>
            <a href="{{ route('vote.page') }}">Vote</a>
            <a href="/blog">L'Actu</a>

            @auth
                @php
                    $panier = \App\Models\Panier::where('id_user_connecte', Auth::id())->first();
                    $totalQuantite = $panier ? $panier->lignes->sum('quantitee') : 0;
                @endphp
                @if(Auth::user()->id_user_connecte === 62)
                    <a href="{{ route('siege.commandes.index') }}" style="border-bottom: 2px solid #00ff87; font-weight: bold;">
                        service commande
                    </a>
                @endif
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

            @if (Auth::user()->id_user_connecte === 12 || Auth::user()->id_user_connecte === 11 || Auth::user()->id_user_connecte === 13)
                <a style="margin-left: auto;" class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>
                <a style="margin-left: auto;" class="account_creation" href="/localisation_des_ventes"><img src="{{ asset('assets/map.png') }}" alt="Compte"></a>
                <a href="{{ route('theme_vote.store') }}" class="account_creation" style="color: #00ff87; font-weight: bold;">+ Créer Votation</a>
            @endif
            @if (Auth::user()->id_user_connecte === 35)
                <a class="account_creation" href="/gestion-rgpd">Espace DPO </a>
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

                
            @endauth
            @auth
@if (Auth::user()->id_user_connecte == 13)
    <div style="display: flex; align-items: center; gap: 10px; margin-left: 15px;">
        
        <a href="{{ route('service_vente.commandes') }}" class="account_creation">
            <p>Expédition</p>
        </a>

        <a href="{{ route('expedition.demain') }}" class="account_creation" style="background-color: #fff7ed; border: 1px solid #d97706;">
            <i class="fa-solid fa-calendar-check" style="color: #d97706;"></i>
        </a>

        <a href="{{ route('expedition.historique') }}" class="account_creation" style="background-color: #f3f4f6; border: 1px solid #9ca3af;" title="Historique">
            <i class="fa-solid fa-clock-rotate-left" style="color: #4b5563;"></i>
        </a>
        <a href="{{ route('expedition.domicile_proche') }}" class="account_creation" style="background-color: #e0f2fe; border: 1px solid #0ea5e9;" title="Domicile Urgent">
            <i class="fa-solid fa-house-chimney-user" style="color: #0369a1;"></i>
        </a>

    </div>
@endif
@endauth
        </nav>
    </header>