<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Pour les logs
use Carbon\Carbon;
use App\Models\User_connecte;
use Twilio\Rest\Client; // Pour Twilio

class LoginController extends Controller
{
    // AFFICHER le formulaire 
    public function formulaire()
    {
        return view('account_connection');
    }

    // TRAITER le formulaire
    public function traitement()
    {
        // 1. Validation
        request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Récupération utilisateur
        $user = User_connecte::where('courriel_user_connecte', request('email'))->first();

        // 3. Vérification mot de passe
        if ($user && Hash::check(request('password'), $user->password_user_connecte)) {

            // === DÉBUT MFA (Double Authentification) ===
            if ($user->mfa_active) {
                
                // A. Générer le code
                $code = rand(100000, 999999);
                
                // B. Sauvegarder en base
                $user->update([
                    'mfa_code' => $code,
                    'mfa_expiration' => Carbon::now()->addMinutes(10)
                ]);

                // C. DOUBLE ACTION : LOG + SMS 📡
                
                // --- ACTION 1 : On écrit dans les LOGS (Au cas où le SMS échoue) ---
                $numero = $user->numero_telephone_user_connecte;
                Log::info("📝 [MFA BACKUP] Code pour {$numero} : {$code}");

                // --- ACTION 2 : On envoie le SMS RÉEL (Twilio) ---
                try {
                    $message = "FIFA ID : Votre code de sécurité est {$code}";

                    $client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
                    
                    $client->messages->create(
                        $numero, 
                        [
                            'from' => env('TWILIO_FROM'),
                            'body' => $message
                        ]
                    );

                    Log::info("✅ SMS Twilio envoyé avec succès.");

                } catch (\Exception $e) {
                    // Si Twilio échoue, on ne bloque pas l'utilisateur, car le code est dans les logs !
                    Log::error("❌ Erreur SMS Twilio : " . $e->getMessage());
                }

                // D. Redirection
                session(['mfa_user_id' => $user->id_user_connecte]);
                return redirect()->route('mfa.form');
            }
            // === FIN MFA ===

            // Connexion directe si pas de MFA
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