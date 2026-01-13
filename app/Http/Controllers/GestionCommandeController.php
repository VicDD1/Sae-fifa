<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Commande;

class GestionCommandeController extends Controller
{   
    // Story 2 & Partielle 5 : Liste globale pour le siège
   public function index()
{
    // [STORY 3] Automatisation compatible PostgreSQL
    \App\Models\Commande::where('statut_livraison', 'Réserve')
            ->where('date_commande', '<=', now()->subDays(15))
            ->update([
                'statut_livraison' => 'Accepté',
                'commentaire_sav' => \DB::raw("CONCAT(COALESCE(commentaire_sav, ''), ' [Auto-clôture 15j]')")
            ]);

    $commandes = \App\Models\Commande::orderBy('id_commande', 'desc')->get();
    return view('gestion_commande', compact('commandes'));
}

    // Story 1, 2 et 4 : Traitement des actions (Acceptation, Refus, Réserve)
    public function updateStatut(Request $request, $id)
{
    // 1. On récupère la commande
    $commande = Commande::where('id_commande', $id)->firstOrFail();

    // 2. Vérification du motif pour les Stories 1 et 4
    if (($request->statut_livraison === 'Refusé' || $request->statut_livraison === 'Réserve') 
        && empty($request->commentaire_sav)) {
        
        return back()->with('error', 'Un motif est obligatoire pour un refus ou une réserve.');
    }

    // 3. Mise à jour des données
    $commande->statut_livraison = $request->statut_livraison;
    $commande->commentaire_sav = $request->commentaire_sav;

    // Story 5 : Enregistrement de la date si accepté
    if ($request->statut_livraison === 'Accepté') {
        $commande->date_livraison_reelle = now();
    }

    $commande->save();

    return back()->with('success', 'Statut de la commande #' . $id . ' mis à jour.');
}

    // Story 5 : Affichage spécifique pour la qualité Express
   public function rapportQualite()
{
    // [STORY 5] Filtrage sur l'ID 3 (Express 24h) d'après tes données SQL
    $commandesExpress = \App\Models\Commande::where('id_mode_livraison', 3) 
    ->orderBy('id_commande', 'desc')
    ->get();

    return view('rapport_qualite', compact('commandesExpress'));
}
}
