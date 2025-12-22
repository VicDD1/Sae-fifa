<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Panier;
use App\Models\Ligne_panier;
use Illuminate\Support\Facades\Auth;

class Ligne_PanierController extends Controller
{
    // Afficher le panier (en base)
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $panier = Panier::where('id_user_connecte', Auth::id())->first();

        if (!$panier) {
            return view('panier', ['lignes' => [], 'total' => 0]);
        }

        $lignes = $panier->lignes;

        $total = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);

        return view('panier', compact('lignes', 'total'));
    }

    // Ajouter un produit au panier
    public function ajouter(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour ajouter un article.');
        }

        $produit = Produit::findOrFail($id);

        // Récupérer ou créer le panier BDD
        $panier = Panier::firstOrCreate(
            ['id_user_connecte' => Auth::id()],
            ['id_acheteur' => Auth::id()]
        );

        $taille = $request->input('id_taille');
        $couleur = $request->input('id_colori');

        // Vérifier si la ligne existe
        $ligne = Ligne_panier::where('id_panier', $panier->id_panier)
            ->where('id_produit', $id)
            ->where('id_taille', $taille)
            ->where('id_colori', $couleur)
            ->first();

        if ($ligne) {
            $ligne->increment('quantitee');
        } else {
            Ligne_panier::create([
                'id_panier' => $panier->id_panier,
                'id_produit' => $id,
                'id_taille' => $taille,
                'id_colori' => $couleur,
                'quantitee' => 1,
            ]);
        }

        return redirect()->back()->with('success', 'Produit ajouté au panier.');
    }

    // Supprimer une ligne
    public function supprimer($id_ligne)
    {
        $ligne = Ligne_panier::findOrFail($id_ligne);

        if ($ligne->panier->id_user_connecte === Auth::id()) {
            $ligne->delete();
        }

        return redirect()->back()->with('success', 'Ligne supprimée.');
    }

    // Modifier quantité
    public function updateQuantite($id_ligne, $action)
    {
        $ligne = Ligne_panier::findOrFail($id_ligne);

        if ($action === 'plus') {
            $ligne->increment('quantitee');
        } elseif ($action === 'minus' AND $ligne->quantitee > 1) {
            $ligne->decrement('quantitee');
        }

        return redirect()->back();
    }
}
