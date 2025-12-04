<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // 2. Tentative de connexion
        
        $resultat = Auth::attempt([
            'courriel_user_connecte' => request('email'),
            'password' => request('password')
        ]);

        if ($resultat) {
            // Connexion réussie !
            request()->session()->regenerate();
            return redirect('/'); // Redirection vers l'accueil
        }

        // 3. Échec
        return back()->withErrors([
            'email' => "L'email ou le mot de passe est incorrect.",
        ])->onlyInput('email');
    }
    
    // Ajout utile : Déconnexion
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}