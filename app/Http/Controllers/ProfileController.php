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
    
        // Assurez-vous d'avoir bien récupéré l'utilisateur archive avant
DB::transaction(function () use ($userToDelete, $archiveUser) {
    $oldUserId = $userToDelete->id_user_connecte;
    $newUserId = $archiveUser->id_user_connecte;

    // 1. Récupérer ou créer le profil acheteur de l'archive
    $archiveAcheteur = DB::table('acheteur')->where('id_user_connecte', $newUserId)->first();
    
    if (!$archiveAcheteur) {
        // On récupère le nouvel ID acheteur créé
        $newAcheteurId = DB::table('acheteur')->insertGetId([
            'id_user_connecte' => $newUserId,
            'telephone_acheteur' => '0000000000',
            'adresse_livraison' => 'Archive'
        ], 'id_acheteur');
    } else {
        $newAcheteurId = $archiveAcheteur->id_acheteur;
    }

    // 2. TRANSFÉRER LES DONNÉES À CLÉS COMPOSÉES (User + Acheteur)
    
    // Transférer les Commandes
    DB::table('commande')
        ->where('id_user_connecte', $oldUserId)
        ->update([
            'id_user_connecte' => $newUserId,
            'id_acheteur'      => $newAcheteurId
        ]);

    // Transférer le Panier (C'est l'étape qui manquait !)
    DB::table('panier')
        ->where('id_user_connecte', $oldUserId)
        ->update([
            'id_user_connecte' => $newUserId,
            'id_acheteur'      => $newAcheteurId
        ]);

    // 3. TRANSFÉRER LE RESTE (Uniquement User ID)
    DB::table('commentaire')->where('id_user_connecte', $oldUserId)->update(['id_user_connecte' => $newUserId]);
    DB::table('voter')->where('id_user_connecte', $oldUserId)->update(['id_user_connecte' => $newUserId]);

    // 4. NETTOYAGE ET SUPPRESSION
    // Maintenant que les commandes ET les paniers sont rattachés à l'archive...
    DB::table('acheteur')->where('id_user_connecte', $oldUserId)->delete(); // Plus de violation ici !
    
    $userToDelete->delete();
    });
        // 3. Déconnexion et redirection
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/')->with('status', 'Votre compte a été supprimé avec succès.');
    }
    public function anonime(Request $request){
        // Récupérer l'utilisateur connecté
    $user = Auth::user();

    try {
        DB::transaction(function () use ($user) {
            $userId = $user->id_user_connecte;

            // 1. ANONYMISATION des données personnelles
            // On utilise update sur l'instance $user pour déclencher les éventuels observateurs Eloquent
            $user->update([
                'prenom_user_connecte'            => 'Anonyme',
                'courriel_user_connecte'          => 'anonyme_' . $userId . '@example.com',
                'date_de_naissance_user_connecte' => '1900-01-01',
                'pays_de_naissance_user_connecte' => 'Inconnu',
                'surnom_user_connecte'            => null,
                'numero_telephone_user_connecte'  => '0000000000',
                'password_user_connecte'          => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)), 
                'mfa_active'                      => 0,
                'mfa_code'                        => null,
                'updated_at'                      => now()
            ]);

            // 2. GESTION DES DONNÉES DE NAVIGATION (Paniers non validés)
            // On récupère les IDs liés à cet utilisateur
            $acheteurIds = DB::table('acheteur')->where('id_user_connecte', $userId)->pluck('id_acheteur');
            $panierIds   = DB::table('panier')->whereIn('id_acheteur', $acheteurIds)->pluck('id_panier');
            
            if ($panierIds->isNotEmpty()) {
                // On supprime les paniers en cours qui contiennent des infos de session
                DB::table('ligne_panier')->whereIn('id_panier', $panierIds)->delete();
                DB::table('panier')->whereIn('id_acheteur', $acheteurIds)->delete();
            }

            // 3. NETTOYAGE DES INTERACTIONS SOCIALES
            // On supprime les votes pour préserver l'anonymat des opinions
            DB::table('voter')->where('id_user_connecte', $userId)->delete();

            // Note : On ne touche pas à la table 'commande' ni 'acheteur' ici 
            // pour garder l'historique comptable (CA), mais l'utilisateur est maintenant "Anonyme".
        });

        // 4. DÉCONNEXION (Obligatoire car le mot de passe a changé)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Votre compte a été anonymisé avec succès. Vous ne pouvez plus vous connecter.');

    } catch (\Exception $e) {
        // En production, préférez Log::error($e->getMessage())
        return back()->with('error', "Une erreur est survenue lors de l'anonymisation.");
    }
    }
}