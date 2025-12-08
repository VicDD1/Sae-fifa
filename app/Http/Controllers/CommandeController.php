<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Panier;
use App\Models\Ligne_panier;
use App\Models\Commande;
use App\Models\Ligne_commande;
use App\Models\Adresse;

class CommandeController extends Controller
{
    /**
     * Afficher la page de commande
     */
    public function afficher()
    {
        if (!Auth::check()) {
            return redirect('/se_connecter')->with('error', 'Veuillez vous connecter.');
        }

        $panier = Panier::where('id_user_connecte', Auth::id())->first();

        if (!$panier || $panier->lignes->count() == 0) {
            return redirect('/panier')->with('error', 'Votre panier est vide.');
        }

        $lignes = $panier->lignes;

        $total = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);

        return view('vue_commande', compact('lignes', 'total'));
    }

    /**
     * Valider et enregistrer la commande
     */
    public function valider(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/se_connecter')->with('error', 'Veuillez vous connecter.');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'cp' => 'required|string|max:20',
            'telephone' => 'required|string|max:20',
            'paiement' => 'required|string',
        ]);

        // Récupération du panier
        $panier = Panier::where('id_user_connecte', Auth::id())->firstOrFail();
        $lignesPanier = $panier->lignes;

        $total = $lignesPanier->sum(fn($l) => $l->quantitee * $l->produit->prix_base);

        // 1. ENREGISTREMENT ADRESSE
        $adresse = Adresse::create([
            'pays_adresse' => $request->pays,       
            'code_postal'  => $request->cp,
            'ville_adresse'=> $request->ville,
        ]);
        

        // 2. CREATION COMMANDE
        $commande = Commande::create([
            'id_adresse'       => $adresse->id_adresse,
            'id_user_connecte' => Auth::id(),
            'id_acheteur'      => Auth::id(),
            'id_mode_livraison'=> 1, // par défaut
            'date_commande'    => now(),
            'montant_total'    => $total,
            'date_paiement'    => now(),
            'mode_paiement'    => $request->paiement,
            'statut_paiement'  => 'En attente',
        ]);

        // 3. CREATION DES LIGNES DE COMMANDE
        foreach ($lignesPanier as $ligne) {
            Ligne_commande::create([
                'id_commande' => $commande->id_commande,
                'id_produit'  => $ligne->id_produit,
                'id_colori'   => $ligne->id_colori,
                'id_taille'   => $ligne->id_taille,
                'quantitee'   => $ligne->quantitee,
            ]);
        }

        // 4. VIDER LE PANIER
        Ligne_panier::where('id_panier', $panier->id_panier)->delete();

        return redirect('/commande/confirmation')->with('success', 'Votre commande a été validée.');
    }

    public function confirmation()
    {
        return view('confirmation_commande');
    }
}
