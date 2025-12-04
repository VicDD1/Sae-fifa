<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Affiche la page de profil de l'utilisateur connecté
    public function show()
    {
        // On récupère SEULEMENT l'utilisateur connecté
        $user = Auth::user();

        // On envoie ses infos à la vue 'account_profile'
        return view('account_profile', compact('user'));
    }

    // 2. Affiche le formulaire de modification
    public function edit()
    {
        $user = Auth::user();
        return view('account_edit', compact('user')); // On crée cette vue juste après
    }

    // 3. Traite la sauvegarde des modifications
    public function update(Request $request)
    {
        $user = Auth::user(); // L'utilisateur à modifier

        // A. Validation des données
        $request->validate([
            'prenom'   => 'required|string|max:50',
            'surnom'   => 'nullable|string|max:50',
            // On vérifie que l'email est unique mais on ignore l'ID de l'utilisateur actuel
            'email'    => 'required|email|unique:user_connecte,courriel_user_connecte,'.$user->id_user_connecte.',id_user_connecte',
            'date_naissance' => 'nullable|string', // Ou date selon ton format BDD
            'pays'     => 'nullable|string',
            'langue'   => 'nullable|string',
            'favori'   => 'nullable|string',
            'password' => 'nullable|min:6', // Mot de passe optionnel
        ]);

        // B. Mise à jour des champs
        $user->prenom_user_connecte = $request->prenom;
        $user->surnom_user_connecte = $request->surnom;
        $user->courriel_user_connecte = $request->email;
        $user->date_de_naissance_user_connecte = $request->date_naissance;
        
        $user->pays_de_naissance_user_connecte = $request->pays_de_naissance_user_connecte;
        $user->langue_user_connecte = $request->langue_user_connecte;
        $user->favori_user_connecte = $request->favori_user_connecte;

        // C. Cas spécial du Mot de Passe
        // On ne le change QUE si l'utilisateur a écrit quelque chose dedans
        if ($request->filled('password')) {
            $user->password_user_connecte = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        // D. Sauvegarde
        $user->save();

        return redirect('/')->with('success', 'Votre profil a bien été mis à jour!');
    }
}