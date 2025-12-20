<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User_connecte; 

class ResetPasswordController extends Controller
{
    // 1. Affiche la page "Entrez votre email"
    public function showLinkRequestForm() {
        return view('auth.email_form'); 
    }

    // 2. Vérifie l'email, crée le token et envoie le mail
    public function sendResetLink(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:user_connecte,courriel_user_connecte'
        ], ['email.exists' => "Aucun compte trouvé avec cet email."]);

        $token = Str::random(64);

        // Nettoyage des anciennes demandes
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        
        // Insertion du nouveau token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // Envoi du mail
        Mail::send('auth.emails.reset_link', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Réinitialisation de mot de passe');
        });

        return back()->with('success', 'Lien envoyé ! Vérifiez vos emails (et spams).');
    }

    // 3. Affiche la page "Nouveau mot de passe"
    public function showResetForm($token) {
        return view('auth.reset_form', ['token' => $token]);
    }

    // 4. Change le mot de passe final
    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:user_connecte,courriel_user_connecte',
            'password' => 'required|min:12|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'token' => 'required'
        ]);

        // Vérification du token
        $checkToken = DB::table('password_reset_tokens')
                        ->where(['email' => $request->email, 'token' => $request->token])
                        ->first();

        if (!$checkToken) {
            return back()->with('error', 'Ce lien est invalide ou a expiré.');
        }

        // Mise à jour de TA table utilisateurs
        User_connecte::where('courriel_user_connecte', $request->email)
            ->update(['password_user_connecte' => Hash::make($request->password)]);

        // Suppression du token utilisé
        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();

        return redirect('/')->with('success', 'Mot de passe modifié avec succès ! Connectez-vous.');
    }
}