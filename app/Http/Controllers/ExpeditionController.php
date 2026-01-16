<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Commande; // Important : on importe le modèle Commande
use Illuminate\Support\Facades\Auth;

class ExpeditionController extends Controller
{
    /**
     * Affiche la liste des commandes prêtes à être expédiées
     */
    public function index()
    {
        // 1. Sécurité : Seul le staff (ID 11 ou 12) peut voir ça
        if (Auth::user()->id_user_connecte !== 13) {
            return redirect('/')->with('error', 'Accès non autorisé');
        }

        // 2. On récupère les commandes "en_preparation" (payées mais pas encore parties)
        $commandesPretes = Commande::with('user') // On charge les infos du client
                            ->where('statut_paiement', 'En attente') // Ou 'Payé', selon votre BDD
                            // ->where('statut', 'en_preparation') // Activez ça si vous avez une colonne statut dédiée
                            ->orderBy('date_commande', 'asc') // Les plus vieilles d'abord
                            ->get();

        // 3. On retourne la vue (le fichier blade que vous avez créé)
        return view('service_vente_commandes', compact('commandesPretes'));
    }

    /**
     * Valide qu'une commande a été remise au transporteur
     */
public function livraisonsDemain()
    {
        if (Auth::user()->id_user_connecte != 13) {
            return redirect('/')->with('error', 'Accès réservé.');
        }

        $demain = \Carbon\Carbon::tomorrow();
        $idModeAutre = 3; // Vérifiez que c'est le bon ID pour "Autre"

$commandesDemain = Commande::with('user')
        // MODIFICATION ICI : On utilise whereIn pour accepter les deux statuts
        ->whereIn('statut_paiement', ['Paye', 'Expediee']) 
        
        ->where('id_mode_livraison', 3) // Votre ID pour "Autre"
        // ->whereBetween(...) // Vos filtres de date
        ->orderBy('statut_paiement', 'desc') // Astuce : Trie pour mettre les "Paye" en premier
        ->get();

    return view('service_vente_autre', compact('commandesDemain'));
    }

    // 2. Action de Validation : On met 'Expediee' (sans accent)
    public function validerEnlevement($id)
{
    $commande = Commande::findOrFail($id);
    $commande->statut_paiement = 'Expediee'; // On enregistre le statut
    $commande->save();
    return redirect()->back();
}

    // 3. Page Historique : On cherche 'Expediee' (sans accent)
    public function historique()
    {
        if (Auth::user()->id_user_connecte != 13) {
            return redirect('/')->with('error', 'Accès réservé.');
        }

        $commandesEnvoyees = Commande::with('user')
            // IMPORTANT : Exactement la même orthographe que dans validerEnlevement
            ->where('statut_paiement', 'Expediee') 
            ->orderBy('date_commande', 'desc')
            ->get();

        return view('service_vente_historique', compact('commandesEnvoyees'));
    }
public function livraisonsDomicileProche()
{
    // Sécurité
    if (Auth::user()->id_user_connecte != 13) {
        return redirect('/')->with('error', 'Accès réservé.');
    }

    $maintenant = \Carbon\Carbon::now();
    $debutPlage = null;
    $finPlage = null;
    $titrePlage = "";

    // --- 1. DÉFINITION DE LA PÉRIODE (Logique Demi-journée) ---
    if ($maintenant->hour < 12) {
        // MATIN (avant 12h) -> On veut CET APRÈS-MIDI (12h-Minuit)
        $debutPlage = \Carbon\Carbon::today()->setTime(12, 0, 0); // Correction atTime -> setTime
        $finPlage   = \Carbon\Carbon::today()->endOfDay();
        $titrePlage = "Cet Après-midi";
    } else {
        // APRÈS-MIDI (après 12h) -> On veut DEMAIN MATIN (Minuit-12h)
        $debutPlage = \Carbon\Carbon::tomorrow()->startOfDay();
        $finPlage   = \Carbon\Carbon::tomorrow()->setTime(12, 0, 0); // Correction atTime -> setTime
        $titrePlage = "Demain Matin";
    }

    // --- 2. RÉCUPÉRATION DES COMMANDES ---
    $idModeDomicile = 1; // ID pour "Transport à Domicile"

    $commandes = Commande::with('user')
        ->whereIn('statut_paiement', ['Paye', 'Expediee']) 
        ->where('id_mode_livraison', $idModeDomicile)
        
        // --- FILTRE ACTIVÉ ICI ---
        // On ne prend que celles dont la date de livraison prévue tombe dans la plage calculée
        ->whereBetween('date_commande', [$debutPlage, $finPlage])
        
        ->orderBy('statut_paiement', 'desc')
        ->get();

    return view('service_vente_domicile', compact('commandes', 'titrePlage'));
}
    
}