<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_connecte; 
use Illuminate\Support\Facades\Auth;

class RGPDController extends Controller
{
    // Affiche la liste filtrée par date
    public function index(Request $request)
    {
        if (Auth::user()->id_user_connecte !== 35) {
            abort(403);
        }

        $dateChoisie = $request->input('date_limite');
        $users = null;

        if ($dateChoisie) {
            // On filtre par la colonne created_at (ou une autre colonne de date si tu préfères)
            // On exclut les IDs importants pour éviter les erreurs
            $users = User_connecte::where('created_at', '<', $dateChoisie)
                                   ->get();
        }

        return view('rgpd', compact('users', 'dateChoisie'));
    }

    // Supprime uniquement les utilisateurs cochés
    public function destroy(Request $request)
    {
        if (Auth::user()->id_user_connecte !== 35) {
            abort(403);
        }

        $ids = $request->input('user_ids');

        if (!$ids || count($ids) === 0) {
            return redirect()->back()->with('error', 'Veuillez cocher au moins un utilisateur.');
        }

        // Suppression définitive des comptes sélectionnés
        User_connecte::whereIn('id_user_connecte', $ids)->delete();

        return redirect()->route('rgpd.gestion')->with('success', count($ids) . " compte(s) supprimé(s) avec succès.");
    }
}