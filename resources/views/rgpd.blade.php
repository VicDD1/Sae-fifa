<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/rgpd.css') }}">
    <title>Gestion RGPD - FIFA Store</title>
</head>
<body>
@include('header')
    
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