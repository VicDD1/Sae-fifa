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
        // On récupère toutes les commandes avec les infos liées
        $commandes = Commande::orderBy('date_commande', 'desc')->get();
    
        return view('gestion_commande', compact('commandes'));
    }

    // Story 1, 2 et 4 : Traitement des actions (Acceptation, Refus, Réserve)
    public function updateStatut(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);
        
        // On met à jour selon le bouton cliqué
        $commande->statut_livraison = $request->statut; 
        
        // Si c'est un refus ou une réserve, on enregistre le motif (Story 1 & 4)
        if ($request->has('motif')) {
            $commande->commentaire_sav = $request->motif;
        }

        $commande->save();
        return back()->with('success', 'Statut mis à jour.');
    }

    // Story 5 : Affichage spécifique pour la qualité Express
    public function rapportQualite()
    {
        // On filtre sur le mode de livraison "Express"
        // (Vérifie l'ID correspondant dans ta table mode_livraison)
        $commandesExpress = Commande::where('id_mode_livraison', 2) 
            ->select('id_commande', 'date_commande', 'date_livraison')
            ->get();

        return view('siege.commandes.qualite', compact('commandesExpress'));
    }
}
