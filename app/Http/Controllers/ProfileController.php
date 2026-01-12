<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User_connecte;
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
        $eighteenYearsAgo = Carbon::now()->subYears(18)->toDateString();
        $user = Auth::user(); // L'utilisateur à modifier

        
        $request->validate([
            'prenom_user_connecte'   => 'nullable|string|max:50',
            'surnom_user_connecte'   => 'nullable|string|max:50',
            // On vérifie que l'email est unique mais on ignore l'ID de l'utilisateur actuel
            'courriel_user_connecte'    => 'nullable|email:dns|unique:user_connecte,courriel_user_connecte,'.$user->id_user_connecte.',id_user_connecte',
            'date_de_naissance_user_connecte' => ['required', 'before_or_equal:' .  $eighteenYearsAgo], // Ou date selon ton format BDD
            'pays_de_naissance_user_connecte'     => 'nullable|string',
            'langue_user_connecte'   => 'nullable|string',
            'favori_user_connecte'   => 'nullable|string',
            'password_user_connecte' => 'nullable|min:12|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', // Mot de passe optionnel
        ],[
            'password_user_connecte.min' => "Le mot de passe doit contenir au moins 12 caractères.",
            'password_user_connecte.regex'=> "le mot de passe doit contenir une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial",
            'password_user_connecte.confirmed' => "Les mots de passe ne correspondent pas.",
            'courriel_user_connecte.unique' => "Cette adresse e-mail est déjà utilisé. Veuillez en choisir un autre.",
            'courriel_user_connecte.email' => "Veuillez saisir une adresse e-mail valide.",
            'date_de_naissance_user_connecte.before_or_equal' => "vous devez avoir au moins 18 ans pour creer un compte",
            
        ]);

        // B. Mise à jour des champs
        $user->prenom_user_connecte = $request->prenom_user_connecte;
        $user->surnom_user_connecte = $request->surnom_user_connecte;
        $user->courriel_user_connecte = $request->courriel_user_connecte;
        $user->date_de_naissance_user_connecte = $request->date_de_naissance_user_connecte;
        $user->pays_de_naissance_user_connecte = $request->pays_de_naissance_user_connecte;
        $user->langue_user_connecte = $request->langue_user_connecte;
        $user->favori_user_connecte = $request->favori_user_connecte;

        // C. Cas spécial du Mot de Passe
        // On ne le change QUE si l'utilisateur a écrit quelque chose dedans
        if ($request->filled('password_user_connecte')) {
            $user->password_user_connecte = \Illuminate\Support\Facades\Hash::make($request->password_user_connecte);
        }

        $user->updated_at = \Carbon\Carbon::now();
        $user->save();

        return redirect('/')->with('success', 'Votre profil a bien été mis à jour !');
    }
    public function delete(Request $request){
        $userToDelete = $request->user();

        // 1. Récupérer l'utilisateur "Fourre-tout"
        // On le cherche par son email fixe (défini dans le Seeder plus haut)
        $archiveUser = User_connecte::where('courriel_user_connecte', 'Suppri@gmail.com')->first();
    
        if (!$archiveUser) {
            // Sécurité : Si le compte archive n'existe pas, on bloque tout !
            return back()->withErrors(['error' => 'Erreur système : Impossible de supprimer le compte pour le moment.']);
        }
    
        // 2. Démarrer la transaction
        DB::transaction(function () use ($userToDelete, $archiveUser) {
    
            // A. LE TRANSFERT (Le cœur de ta méthode)
            // On prend toutes les commandes de l'utilisateur actuel
            // Et on remplace son ID par celui de l'utilisateur Archive
            $userToDelete->orders()->update(['id_user_connecte' => $archiveUser->id]);
    
            // Optionnel : Transférer d'autres trucs (ex: Commentaires, Tickets support...)
            // $userToDelete->comments()->update(['user_id' => $archiveUser->id]);
    
            // B. LA SUPPRESSION
            // Maintenant qu'il n'a plus rien, on peut le supprimer proprement.
            $userToDelete->delete();
        });
    
        // 3. Déconnexion et redirection
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/')->with('status', 'Votre compte a été supprimé avec succès.');
    }
    public function anonime(){
        //
    }
}