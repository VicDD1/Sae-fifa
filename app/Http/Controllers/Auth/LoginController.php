<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Pour vérifier le mot de passe
use Illuminate\Support\Facades\Log;  // Pour simuler le SMS
use Carbon\Carbon;                   // Pour gérer l'expiration du code
use App\Models\User_connecte;        // Ton modèle utilisateur

class LoginController extends Controller
{
    // Fonction pour AFFICHER le formulaire 
    public function formulaire()
    {
        return view('account_connection');
    }

    // Fonction pour TRAITER le formulaire
    public function traitement()
    {
        // 1. Validation
        request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. On récupère l'utilisateur par son email (sans le connecter)
        $user = User_connecte::where('courriel_user_connecte', request('email'))->first();

        // 3. On vérifie si l'utilisateur existe ET si le mot de passe est bon
        if ($user && Hash::check(request('password'), $user->password_user_connecte)) {

            // === DÉBUT DE LA LOGIQUE MFA (Double sécurité) ===
            
            // Si le MFA est activé (true)
            if ($user->mfa_active) {
                
                // A. Générer un code (6 chiffres)
                $code = rand(100000, 999999);
                
                // B. Enregistrer le code en base (expire dans 10 min)
                $user->update([
                    'mfa_code' => $code,
                    'mfa_expiration' => Carbon::now()->addMinutes(10)
                ]);

                // C. SIMULATION SMS REALISTE 📱
                // On récupère le numéro de téléphone pour prouver qu'on l'a bien trouvé
                $numero = $user->numero_telephone_user_connecte;
                
                // On écrit dans le fichier log comme si on était l'opérateur
                Log::info("🚀 [SIMULATION SMS] Envoi au numéro {$numero} : Votre code FIFA ID est {$code}");

                // D. Mettre l'ID en session "d'attente"
                session(['mfa_user_id' => $user->id_user_connecte]);

                // E. Rediriger vers la page du code
                return redirect()->route('mfa.form');
            }
            // === FIN DE LA LOGIQUE MFA ===

            // Si pas de MFA, on connecte l'utilisateur directement
            Auth::login($user);
            request()->session()->regenerate();
            
            return redirect('/'); // Connexion réussie
        }

        // 4. Échec (Mauvais mot de passe ou email inconnu)
        return back()->withErrors([
            'email' => "L'email ou le mot de passe est incorrect.",
        ])->onlyInput('email');
    }
    
    // Déconnexion
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}