<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Expédition - FIFA</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="antialiased">

    <header>
        <nav>
            <a href="/"> <img style="width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Accueil"></a>
            <a href="/produits">Fifa Store</a>
            <a href="{{ route('vote.page') }}">Vote</a>
            <a href="/players">Les joueurs</a>
            <a href="/blog">L'Actu</a>

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
                        <img style="width:40px;" src="{{asset('assets/iconEdit.png')}}" alt="Info">
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
        </nav>
    </header>

    <div style="padding: 40px; max-width: 1200px; margin: 0 auto; min-height: 60vh;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 15px;">
            <h1 style="margin: 0; color: #333;"><i class="fa-solid fa-boxes-packing" style="color: #2563eb;"></i> Espace Expédition</h1>
            <span style="background: #2563eb; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 14px;">
                {{ $commandesPretes->count() }} colis en attente
            </span>
        </div>

        @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 15px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #a7f3d0; display: flex; align-items: center;">
                <i class="fa-solid fa-check-circle" style="margin-right: 10px; font-size: 20px;"></i>
                {{ session('success') }}
            </div>
        @endif

        <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 15px; text-align: left; color: #475569;">N° Commande</th>
                        <th style="padding: 15px; text-align: left; color: #475569;">Client</th>
                        <th style="padding: 15px; text-align: left; color: #475569;">Date</th>
                        <th style="padding: 15px; text-align: center; color: #475569;">Action Transporteur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commandesPretes as $commande)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 15px; font-weight: bold; color: #2563eb;">#{{ $commande->id_commande }}</td>
                            <td style="padding: 15px;">
                                <div style="font-weight: 600;">{{ $commande->user->prenom_user_connecte ?? 'Client' }} {{ $commande->user->nom_user_connecte ?? '' }}</div>
                                <div style="font-size: 12px; color: #64748b;">ID Client: {{ $commande->id_user_connecte }}</div>
                            </td>
                            <td style="padding: 15px; color: #64748b;">
                                {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <form action="{{ route('expedition.valider', $commande->id_commande) }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background-color: #10b981; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 6px; font-weight: bold; font-size: 13px; transition: background 0.3s;">
                                        <i class="fa-solid fa-truck-ramp-box"></i> VALIDER ENLÈVEMENT
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 50px; text-align: center; color: #94a3b8;">
                                <i class="fa-solid fa-clipboard-check" style="font-size: 40px; margin-bottom: 15px; display: block; color: #cbd5e1;"></i>
                                Aucune commande en attente d'expédition.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <a href="{{ route('cookies.manage') }}">Gérer mes cookies</a>
        <span>|</span>
        <a href="/privacy_policy"> Conditions d'utilisation </a>
        <span>|</span>
         <a href="/privacy_policy"> Respect de la vie privée </a> 
    </footer>

</body>
</html>