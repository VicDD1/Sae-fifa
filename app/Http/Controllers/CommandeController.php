<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Panier;
use App\Models\Ligne_panier;
use App\Models\Commande;
use App\Models\Ligne_commande;
use App\Models\Adresse;
use App\Models\Acheteur;
use App\Models\Carte_Bancaire;
use App\Models\Mode_livraison;

class CommandeController extends Controller
{
    /**
     * Étape 1 : Affichage de la page commande
     */
    public function afficher()
    {
        if (!Auth::check()) {
            return redirect('/se_connecter')->with('error', 'Veuillez vous connecter.');
        }

        $panier = Panier::where('id_user_connecte', Auth::id())->first();

        if (!$panier || $panier->lignes->count() === 0) {
            return redirect('/panier')->with('error', 'Votre panier est vide.');
        }

        $lignes = $panier->lignes;
        $total = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);

        $modes = Mode_livraison::all();

        return view('vue_commande', compact('lignes', 'total', 'modes'));
    }


    /**
     * Étape 2 : Validation informations + adresse avant confirmation
     */
    public function valider(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/se_connecter')->with('error', 'Veuillez vous connecter.');
        }

        $request->validate([
            'nom'       => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'ville'     => 'required|string|max:255',
            'cp'        => 'required|string|max:20',
            'telephone' => 'required|string|max:20',
            'paiement'  => 'required|string',
            'mode_livraison' => 'required|integer|exists:mode_livraison,id_mode_livraison',
        ]);

        $panier = Panier::where('id_user_connecte', Auth::id())->firstOrFail();
        $lignes = $panier->lignes;
        $total  = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);

        $mode = Mode_livraison::find($request->mode_livraison);

        return view('confirmation_commande', [
            'lignes' => $lignes,
            'total'  => $total + $mode->prix_mode_livraison,
            'data'   => $request->all(),
            'mode'   => $mode
,
        ]);
    }


    /**
     * Étape 3 : Confirmation finale → création réelle de la commande
     */
    public function confirmation(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/se_connecter')->with('error', 'Veuillez vous connecter.');
        }

        $request->validate([
            'nom'       => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'ville'     => 'required|string|max:255',
            'cp'        => 'required|string|max:20',
            'telephone' => 'required|string|max:20',
            'paiement'  => 'required|string',
            'mode_livraison' => 'required|integer|exists:mode_livraison,id_mode_livraison',

            'card_name'   => 'required|string|max:255',
            'card_number' => 'required|string|max:16',
            'expiry'      => 'required|string|max:5',
            'cvv'         => 'required|string|max:3',
        ]);

        $panier = Panier::where('id_user_connecte', Auth::id())->firstOrFail();
        $lignes = $panier->lignes;
        $total  = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);

        $mode = Mode_livraison::findOrFail($request->mode_livraison);
        $total_final = $total + $mode->prix_mode_livraison;


        // ---- Acheteur ----
        $acheteur = Acheteur::where('id_user_connecte', Auth::id())->first();

        if (!$acheteur) {
            $acheteur = Acheteur::create([
                'id_user_connecte' => Auth::id(),
                'telephone_acheteur' => $request->telephone,
                'adresse_livraison' => $request->adresse,
            ]);
        }

        // ---- Adresse ----
        $adresse = Adresse::create([
            'pays_adresse'  => $request->pays ?? 'France',
            'code_postal'   => $request->cp,
            'ville_adresse' => $request->ville,
        ]);

        // ---- Carte bancaire ----
        // ---- Carte bancaire ----
        $carte = Carte_Bancaire::create([
            'id_user_connecte' => Auth::id(),
            'numero_carte'     => $request->card_number,
            'date_expiration'  => $request->expiry,
            'cryptogramme'     => $request->cvv,
            'nom_titulaire'    => $request->card_name,
        ]);


        // ---- Commande ----
        $commande = Commande::create([
            'id_adresse'        => $adresse->id_adresse,
            'id_user_connecte'  => Auth::id(),
            'id_acheteur'       => $acheteur->id_acheteur,
            'id_mode_livraison' => $mode->id_mode_livraison,
            'date_commande'     => now(),
            'montant_total'     => $total_final,
            'date_paiement'     => now(),
            'mode_paiement'     => $request->paiement,
            'statut_paiement'   => 'En attente',
        ]);

        // ---- Lignes commandes ----
        foreach ($lignes as $ligne) {
            Ligne_commande::create([
                'id_commande' => $commande->id_commande,
                'id_produit'  => $ligne->id_produit,
                'id_colori'   => $ligne->id_colori,
                'id_taille'   => $ligne->id_taille,
                'quantitee'   => $ligne->quantitee,
            ]);
        }

        // ---- Vider panier ----
        Ligne_panier::where('id_panier', $panier->id_panier)->delete();

        return redirect()->route('commande.succes');
    }
    public function liste()
    {
        if (!Auth::check()) {
            return redirect('/se_connecter')->with('error', 'Veuillez vous connecter.');
        }

        $commandes = Commande::where('id_user_connecte', Auth::id())
            ->orderBy('date_commande', 'desc')
            ->get();

        return view('mes_commandes', compact('commandes'));
    }

}
