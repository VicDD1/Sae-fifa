<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Pour écrire le SMS dans le fichier log
use Carbon\Carbon;
use App\Models\User_connecte;

class MfaController extends Controller
{
    // 1. Activer le MFA (Quand l'utilisateur est connecté sur son profil)
    public function enableMfa(Request $request)
{
    $user = Auth::user();

    // 1. On nettoie l'entrée (on enlève les espaces, tirets, etc.)
    $inputClean = preg_replace('/[^0-9]/', '', $request->numero_telephone_user_connecte);
    
    // Si c'était un +33..., on remplace par 0 pour la vérification ou on garde tel quel
    // Pour simplifier, on injecte le numéro nettoyé dans la requête pour la validation
    $request->merge(['numero_telephone_user_connecte' => $inputClean]);

    // 2. Validation
    $request->validate([
        'numero_telephone_user_connecte' => 'required|numeric|digits_between:10,15'
    ]);

    // 3. Mise à jour
    $user->update([
        'numero_telephone_user_connecte' => $request->numero_telephone_user_connecte,
        'mfa_active' => true 
    ]);

    return back()->with('success', 'Sécurité activée ! Vous recevrez un code par SMS à la prochaine connexion.');
}

    // 2. Afficher la page "Entrez le code" (Quand on essaie de se connecter)
    public function showMfaForm()
    {
        // Si on n'a pas commencé de connexion, on renvoie au login
        if (!session()->has('mfa_user_id')) {
            return redirect('/login');
        }
        return view('auth.mfa_verify');
    }

    // 3. Vérifier le code reçu
    public function verifyMfa(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        // On récupère l'ID de l'utilisateur qui attend
        $userId = session('mfa_user_id');
        $user = User_connecte::find($userId);

        if (!$user) {
            return redirect('/login')->with('error', 'Session expirée, recommencez.');
        }

        // Vérification : Est-ce le bon code ? Est-il encore valide (temps) ?
        if ($user->mfa_code === $request->code && Carbon::now()->lessThan($user->mfa_expiration)) {
            
            // C'est gagné ! On connecte l'utilisateur
            Auth::login($user);
            
            // On nettoie la session et la base
            session()->forget('mfa_user_id');
            $user->update(['mfa_code' => null, 'mfa_expiration' => null]);

            return redirect('/')->with('success', 'Connexion sécurisée réussie !');
        }

        return back()->with('error', 'Code invalide ou expiré.');
    }
}