<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_connecte; 
use Illuminate\Support\Facades\Auth;

class RGPDController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->id_user_connecte !== 35) {
            abort(403);
        }

        $dateChoisie = $request->input('date_limite');
        $users = null;

        if ($dateChoisie) {
            // Puisque created_at existe maintenant, on l'utilise !
            // On exclut les IDs 11, 12 (admins) et 35 (DPO) pour ne pas les supprimer par erreur
            $users = User_connecte::where('created_at', '<', $dateChoisie)
                                ->whereNotIn('id_user_connecte', [11, 12, 35])
                                ->get();
        }

        return view('rgpd', compact('users', 'dateChoisie'));
    }

public function anonymize(Request $request)
{
    // Sécurité : Seul le DPO (ID 35) peut agir
    if (Auth::user()->id_user_connecte !== 35) {
        abort(403);
    }

    $ids = $request->input('user_ids');

    if (!$ids || count($ids) === 0) {
        return redirect()->back()->with('error', 'Veuillez cocher au moins un utilisateur.');
    }

    try {
        \DB::transaction(function () use ($ids) {
            
            // 1. ANONYMISATION de la table user_connecte
            // On remplace les données sensibles par des valeurs neutres
            \DB::table('user_connecte')
                ->whereIn('id_user_connecte', $ids)
                ->update([
                    'prenom_user_connecte' => 'Anonyme',
                    'courriel_user_connecte' => \DB::raw("CONCAT('anonyme_', id_user_connecte, '@example.com')"),
                    'date_de_naissance_user_connecte' => '1900-01-01',
                    'pays_de_naissance_user_connecte' => 'Inconnu',
                    'surnom_user_connecte' => null,
                    'numero_telephone_user_connecte' => '0000000000',
                    'password_user_connecte' => 'ANONYMISED', // L'utilisateur ne pourra plus se connecter
                    'mfa_active' => 0,
                    'mfa_code' => null,
                    'updated_at' => now()
                ]);

            // 2. GESTION DES COMMANDES / PANIERS
            // Note : Pour l'anonymisation, on NE SUPPRIME PAS les lignes de commande
            // On veut garder le chiffre d'affaires. L'acheteur existe toujours 
            // mais ses infos personnelles sont masquées ci-dessus.
            
            // Si vous voulez quand même supprimer les paniers (qui ne sont pas des ventes) :
            $acheteurIds = \DB::table('acheteur')->whereIn('id_user_connecte', $ids)->pluck('id_acheteur');
            $panierIds = \DB::table('panier')->whereIn('id_acheteur', $acheteurIds)->pluck('id_panier');
            
            if ($panierIds->isNotEmpty()) {
                \DB::table('ligne_panier')->whereIn('id_panier', $panierIds)->delete();
                \DB::table('panier')->whereIn('id_acheteur', $acheteurIds)->delete();
            }

            // 3. NETTOYAGE DES TABLES DE LIAISON SENSIBLES
            \DB::table('voter')->whereIn('id_user_connecte', $ids)->delete();
            // On garde 'acheteur' et 'commande' pour la cohérence comptable.
        });

        return redirect()->route('rgpd.gestion')->with('success', count($ids) . " compte(s) anonymisé(s) avec succès.");

    } catch (\Exception $e) {
        dd("Erreur lors de l'anonymisation : " . $e->getMessage());
    }
}
}