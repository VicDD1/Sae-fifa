<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;


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
     * GET /confirmation_commande
     * Permet de ré-afficher la page de confirmation après un redirect back (erreurs validation),
     * uniquement si les données checkout existent en session.
     */
    public function confirmationPage()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $data = session('checkout.data');
        $modeId = session('checkout.mode_id');

        if (!$data || !$modeId) {
            return redirect()->route('commande.page');
        }

        $panier = Panier::where('id_user_connecte', Auth::id())->first();
        if (!$panier || $panier->lignes->count() === 0) {
            return redirect('/panier')->with('error', 'Votre panier est vide.');
        }

        $lignes = $panier->lignes;
        $total  = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);
        $mode   = Mode_livraison::findOrFail($modeId);
        $cartes = Carte_Bancaire::where('id_user_connecte', Auth::id())->get();

        return view('confirmation_commande', [
            'lignes' => $lignes,
            'total'  => $total + $mode->prix_mode_livraison,
            'data'   => $data,
            'mode'   => $mode,
            'cartes' => $cartes,
        ]);
    }
    /**
     * Étape 1 : Affichage de la page commande
     */
    public function afficher()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
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
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $rules = [
            'nom'       => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'ville'     => 'required|string|max:255',
            'cp'        => 'required|string|max:20',
            'telephone' => 'required|string|max:20',
            'paiement'  => 'required|string',
            'mode_livraison' => 'required|integer|exists:mode_livraison,id_mode_livraison',
        ];

        if ($request->carte_existante === 'nouvelle') {
            $rules['card_name']   = 'required|string|max:255';
            $rules['card_number'] = 'required|string|max:16';
            $rules['expiry']      = 'required|string|max:5';
            $rules['cvv']         = 'required|string|max:3';
        }

        $request->validate($rules);


        

        $panier = Panier::where('id_user_connecte', Auth::id())->firstOrFail();
        $lignes = $panier->lignes;
        $total  = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);
        $cartes = Carte_Bancaire::where('id_user_connecte', Auth::id())->get();

        $mode = Mode_livraison::findOrFail($request->mode_livraison);

        // Stocke les infos checkout en session pour permettre un GET /confirmation_commande
        // (utile quand `confirmation()` renvoie un redirect back après validation).
        session([
            'checkout.data' => $request->all(),
            'checkout.mode_id' => $mode->id_mode_livraison,
        ]);

        return view('confirmation_commande', [
            'lignes' => $lignes,
            'total'  => $total + $mode->prix_mode_livraison,
            'data'   => $request->all(),
            'mode'   => $mode,
            'cartes' => $cartes,
        ]);

    }


    /**
     * Étape 3 : Confirmation finale → création réelle de la commande
     */
    public function confirmation(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $rules = [
            'nom'       => 'required|string|max:255',
            'adresse'   => 'required|string|max:255',
            'ville'     => 'required|string|max:255',
            'cp'        => 'required|string|max:20',
            'telephone' => 'required|string|max:20',
            'paiement'  => 'required|string',
            'mode_livraison' => 'required|integer|exists:mode_livraison,id_mode_livraison',

            // peut être 'nouvelle' ou un id de carte
            'carte_existante' => 'nullable',
        ];

        $useExistingCard = $request->filled('carte_existante') && $request->carte_existante !== 'nouvelle';

        if ($useExistingCard) {
            $rules['carte_existante'] = 'required|integer|exists:carte_bancaire,id_carte';
        } else {
            $rules['card_name']   = 'required|string|max:255';
            $rules['card_number'] = 'required|string|max:16';
            $rules['expiry']      = 'required|string|max:5';
            $rules['cvv']         = 'required|string|max:3';
        }

        $request->validate($rules);

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
        if ($useExistingCard) {
            $carte = Carte_Bancaire::where('id_carte', $request->carte_existante)
                ->where('id_user_connecte', Auth::id())
                ->first();

            if (!$carte) {
                return redirect()->back()->with('error', 'Carte bancaire invalide.');
            }
        } else {
            $pan = preg_replace('/\D+/', '', $request->card_number);

            $carte = Carte_Bancaire::create([
                'id_user_connecte' => Auth::id(),
                'numero_carte'     => Crypt::encryptString($pan),
                'date_expiration'  => Crypt::encryptString($request->expiry),
                'nom_titulaire'    => $request->card_name,
            ]);
        }


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
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $commandes = Commande::with(['modeLivraison', 'adresse', 'lignes.produit'])
            ->where('id_user_connecte', Auth::id())
            ->orderBy('date_commande', 'desc')
            ->get();

        return view('mes_commandes', compact('commandes'));
    }
    public function succes()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        return view('succes_commande');
    }

    public function listeComplete()
{
    // Sécurité : On vérifie que l'utilisateur est bien un admin ou membre du staff
    // (Adaptez l'ID 12/11 selon vos rôles comme vu précédemment)
    if (Auth::user()->id_user_connecte !== 12 && Auth::user()->id_user_connecte !== 11) {
        return redirect('/')->with('error', 'Accès interdit');
    }

    // Récupération de toutes les commandes
    // 'with' permet d'optimiser la requête pour récupérer les infos du client en même temps
    // 'latest' trie par date décroissante (les plus récentes en premier)
    $commandes = Commande::with('user')
                ->latest('created_at')
                ->paginate(20); // Affiche 20 commandes par page

    return view('admin.commandes.index', compact('commandes'));
}
    


}
