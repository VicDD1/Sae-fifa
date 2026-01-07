<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User_connecte;
use Twilio\Rest\Client; // <--- L'import important pour Twilio

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

        // 2. On récupère l'utilisateur
        $user = User_connecte::where('courriel_user_connecte', request('email'))->first();

        // 3. On vérifie user + password
        if ($user && Hash::check(request('password'), $user->password_user_connecte)) {

            // === DÉBUT MFA ===
            if ($user->mfa_active) {
                
                // A. Générer un code (6 chiffres)
                $code = rand(100000, 999999);
                
                // B. Enregistrer le code en base
                $user->update([
                    'mfa_code' => $code,
                    'mfa_expiration' => Carbon::now()->addMinutes(10)
                ]);

                // C. ENVOI RÉEL PAR SMS (TWILIO) 📡
                try {
                    // On récupère le numéro (Il est déjà propre : +336...)
                    $numero = $user->numero_telephone_user_connecte;
                    
                    $message = "FIFA ID : Votre code de sécurité est {$code}";

                    // Connexion à Twilio (récupère les clés dans le .env)
                    $client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
                    
                    // Envoi du message
                    $client->messages->create(
                        $numero, 
                        [
                            'from' => env('TWILIO_FROM'),
                            'body' => $message
                        ]
                    );

                    Log::info("✅ SMS envoyé avec succès au {$numero}");

                } catch (\Exception $e) {
                    // En cas d'erreur (ex: numéro non vérifié en mode gratuit)
                    Log::error("❌ Erreur SMS Twilio : " . $e->getMessage());
                }

                // D. Redirection vers la page du code
                session(['mfa_user_id' => $user->id_user_connecte]);
                return redirect()->route('mfa.form');
            }
            // === FIN MFA ===

            // Si pas de MFA, connexion directe
            Auth::login($user);
            request()->session()->regenerate();
            
            return redirect('/');
        }

        // 4. Échec
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

