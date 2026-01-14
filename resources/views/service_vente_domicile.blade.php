<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Urgence Domicile - FIFA</title>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="antialiased">

    <header>
        <nav>
            <a href="/"> <img style="width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Accueil"></a>
            <a href="/produits">Fifa Store</a>
            
            @if (Auth::user()->id_user_connecte == 13)
                <div style="display: flex; gap: 10px; margin-left: 20px;">
                    <a href="{{ route('service_vente.commandes') }}" class="account_creation">Expédition</a>
                    <a href="{{ route('expedition.demain') }}" class="account_creation">Demain (Autre)</a>
                    <a href="#" class="account_creation" style="background: #e0f2fe; color: #0369a1; border: 1px solid #0ea5e9;">
                        <i class="fa-solid fa-house-chimney-user"></i> Domicile ({{ $titrePlage }})
                    </a>
                </div>
            @endif
             <form action="/logout" method="POST" style="margin-left:auto; margin-right: 20px;">@csrf<button type="submit" style="background:none; border:none; color:white;"><i class="fa-solid fa-power-off"></i></button></form>
        </nav>
    </header>

    <div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 15px;">
            <h1 style="margin: 0; color: #0369a1;">
                <i class="fa-solid fa-stopwatch"></i> Domicile : {{ $titrePlage }}
            </h1>
            <span style="background: #0ea5e9; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold;">
                {{ $commandes->count() }} priorités
            </span>
        </div>

        <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f0f9ff; border-bottom: 2px solid #bae6fd;">
                    <tr>
                        <th style="padding: 15px; text-align: left; color: #075985;">N°</th>
                        <th style="padding: 15px; text-align: left; color: #075985;">Client</th>
                        <th style="padding: 15px; text-align: left; color: #075985;">Date</th>
                        <th style="padding: 15px; text-align: center; color: #075985;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commandes as $commande)
                        <tr style="border-bottom: 1px solid #e2e8f0; {{ $commande->statut_paiement == 'Expediee' ? 'background-color: #f0fdf4;' : '' }}">
                            <td style="padding: 15px; font-weight: bold; color: #0284c7;">#{{ $commande->id_commande }}</td>
                            <td style="padding: 15px;">{{ $commande->user->prenom_user_connecte ?? 'Client' }}</td>
                            <td style="padding: 15px;">{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}</td>
                            <td style="padding: 15px; text-align: center;">
                                @if($commande->statut_paiement == 'Expediee')
                                    <div style="color: #16a34a; font-weight: bold; border: 2px solid #16a34a; padding: 8px 20px; border-radius: 6px; display: inline-block;">
                                        <i class="fa-solid fa-check"></i> PARTI
                                    </div>
                                @else
                                    <form action="{{ route('expedition.valider', $commande->id_commande) }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background-color: #0284c7; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 6px; font-weight: bold;">
                                            <i class="fa-solid fa-truck-fast"></i> ENVOYER
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="padding: 50px; text-align: center; color: #94a3b8;">Rien à livrer pour {{ $titrePlage }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>