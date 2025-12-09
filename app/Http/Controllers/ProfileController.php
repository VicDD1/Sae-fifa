<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import nécessaire pour le mot de passe

class ProfileController extends Controller
{
    // 1. Affiche la page de profil (Lecture seule)
    public function show()
    {
        $user = Auth::user();
        return view('account_profile', compact('user'));
    }

    // 2. Affiche le formulaire de modification
    public function edit()
    {
        $user = Auth::user();
        return view('account_edit', compact('user'));
    }

    // 3. Traite la sauvegarde des modifications
    public function update(Request $request)
    {
        $user = Auth::user(); 

        
        $request->validate([
            'prenom' => 'required|string|max:50',
            'surnom' => 'nullable|string|max:50',
            
            
            'email'  => [
                'required',
                'email:rfc,dns', 
                'unique:user_connecte,courriel_user_connecte,'.$user->id_user_connecte.',id_user_connecte'
            ],
            
            
            'date_naissance' => 'nullable|date|before:-15 years', 
            
           
            'pays_de_naissance_user_connecte' => 'nullable|string',
            'langue_user_connecte' => 'nullable|string',
            'favori_user_connecte' => 'nullable|string',
            
            'password' => 'nullable|min:12', 
        ], [
            
            'date_naissance.before' => 'Vous devez avoir au moins 15 ans.',
            'email.email' => 'Veuillez entrer une adresse email réelle et valide.',
            'email.unique' => 'Cet email est déjà utilisé par un autre compte.'
        ]);

        
        $user->prenom_user_connecte = $request->prenom;
        $user->surnom_user_connecte = $request->surnom;
        $user->courriel_user_connecte = $request->email;
        $user->date_de_naissance_user_connecte = $request->date_naissance;

      
        $user->pays_de_naissance_user_connecte = $request->pays_de_naissance_user_connecte;
        $user->langue_user_connecte = $request->langue_user_connecte;
        $user->favori_user_connecte = $request->favori_user_connecte;

        
        if ($request->filled('password')) {
            $user->password_user_connecte = Hash::make($request->password);
        }

       
        $user->save();

        return redirect('/')->with('success', 'Votre profil a bien été mis à jour !');
    }
}