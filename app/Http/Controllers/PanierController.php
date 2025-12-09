<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Panier;
use App\Models\Ligne_panier;
use Illuminate\Support\Facades\Auth;

class PanierController extends Controller
{
    // ♦ 1. AFFICHER LE PANIER UTILISATEUR
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/se_connecter')
                ->with('error', 'Veuillez vous connecter pour accéder à votre panier.');
        }

        $panier = Panier::where('id_user_connecte', Auth::id())->first();

        if (!$panier) {
            return view('panier', ['lignes' => [], 'total' => 0]);
        }

        $lignes = $panier->lignes;

        $total = $lignes->sum(function($l) {
            return $l->quantitee * $l->produit->prix_base;
        });

        return view('panier', compact('lignes', 'total'));
    }


    // ♦ 2. AJOUTER AU PANIER
    public function ajouter(Request $request, $id_produit)
    {
        if (!Auth::check()) {
            return redirect('/se_connecter')
                ->with('error', 'Vous devez être connecté pour ajouter un article au panier.');
        }

        $produit = Produit::findOrFail($id_produit);

        // Récupération du panier ou création
        $panier = Panier::firstOrCreate(
            ['id_user_connecte' => Auth::id()],
            [
                'id_user_connecte' => Auth::id(),
                'id_acheteur' => Auth::id()
            ]
        );
        
        

        // Récupération des variantes
        $taille = $request->input('id_taille');
        $couleur = $request->input('id_colori');

        // Vérifier si la ligne existe déjà
        $ligne = Ligne_panier::where('id_panier', $panier->id_panier)
            ->where('id_produit', $id_produit)
            ->where('id_taille', $taille)
            ->where('id_colori', $couleur)
            ->first();

        if ($ligne) {
            $ligne->increment('quantitee');
        } else {
            Ligne_panier::create([
                'id_panier'   => $panier->id_panier,
                'id_produit'  => $id_produit,
                'id_colori'   => $couleur,
                'id_taille'   => $taille,
                'quantitee'   => 1,
            ]);
        }

        return redirect()->back()->with('success', 'Produit ajouté au panier');
    }


    // ♦ 3. SUPPRIMER UNE LIGNE
    public function supprimer($id_ligne)
    {
        $ligne = Ligne_panier::findOrFail($id_ligne);

        if ($ligne->id_panier == Panier::where('id_user_connecte', Auth::id())->value('id_panier')) {
            $ligne->delete();
        }

        return redirect()->back()->with('success', 'Article supprimé du panier.');
    }


    // ♦ 4. MODIFIER QUANTITÉ
    public function updateQuantite($id_ligne, $action)
    {
        $ligne = Ligne_panier::findOrFail($id_ligne);

        if ($action === 'plus') {
            $ligne->increment('quantitee');
        } elseif ($action === 'minus' && $ligne->quantitee > 1) {
            $ligne->decrement('quantitee');
        }
        if ($action === 'minus'&& $ligne->quantitee == 1){
            $ligne->delete();
        }

        return redirect()->back();
    }
}
