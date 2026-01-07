<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_connecte; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RGPDController extends Controller
{
    /**
     * Affiche la page de gestion RGPD
     */
    public function index()
    {
        // Sécurité maximale : on revérifie l'ID même si la route est protégée
        if (Auth::user()->id_user_connecte !== 35) {
            abort(403, 'Accès réservé au Délégué à la Protection des Données.');
        }

        return view('rgpd');
    }

    /**
     * Logique d'anonymisation des données
     */
    public function anonymize(Request $request)
    {
        if (Auth::user()->id_user_connecte !== 35) {
            abort(403);
        }

        // 1. Récupérer la date choisie ou 3 ans par défaut
        $dateLimite = $request->input('date_limite', Carbon::now()->subYears(3));

        // 2. Trouver les utilisateurs inactifs (dernière mise à jour avant la date limite)
        // On exclut les admins et le DPO lui-même pour ne pas s'auto-anonymiser !
        $usersToAnonymize = User_connecte::where('updated_at', '<', $dateLimite)
            ->whereNotIn('id_user_connecte', [11, 12, 34]) 
            ->get();

        $count = 0;

        foreach ($usersToAnonymize as $user) {
            $user->update([
                'nom_user_connecte'    => 'ANONYME',
                'prenom_user_connecte' => 'Utilisateur',
                'email_user_connecte'  => 'anonyme_' . $user->id_user_connecte . '@fifa-store.fr',
                'password'             => bcrypt(str_random(40)), // On change le mot de passe par sécurité
                'tel_user_connecte'    => '0000000000',
                // Ajoute ici tous les champs sensibles de ta table
            ]);
            $count++;
        }

        return redirect()->back()->with('success', "$count comptes ont été anonymisés avec succès conformément au RGPD.");
    }
}