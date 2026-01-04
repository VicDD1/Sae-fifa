<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Panier;
use App\Models\Ligne_panier;
use Illuminate\Support\Facades\Auth;
use App\Models\Variante_produit;

class PanierController extends Controller
{
    // ♦ 1. AFFICHER LE PANIER UTILISATEUR
    public function index()
    {
        if (!Auth::check()) {
              return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter pour accéder à votre panier.');
        }

        $panier = Panier::where('id_user_connecte', Auth::id())->first();

        if (!$panier) {
            return view('panier', [
                'lignes' => collect([]),
                'total' => 0
            ]);
        }

        $lignes = collect($panier->lignes);

        $total = $lignes->sum(function($l) {
            return $l->quantitee * $l->produit->prix_base;
        });

        return view('panier', compact('lignes', 'total'));
    }

    // ♦ 2. AJOUTER AU PANIER
    public function ajouter(Request $request, $id_produit)
    {
        if (!Auth::check()) {
              return redirect()->route('login')
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

        // Récupérer le stock pour cette variante
        $stock = Variante_produit::where('id_produit', $id_produit)
            ->where('id_taille', $taille)
            ->where('id_colori', $couleur)
            ->value('quantitee_stock');

        if ($ligne) {
            if ($ligne->quantitee < $stock) {
                $ligne->increment('quantitee');
            } else {
                return redirect()->back()->with('error', 'Impossible d’ajouter plus de ce produit, stock limité.');
            }
        } else {
            if ($stock > 0) {
                Ligne_panier::create([
                    'id_panier'   => $panier->id_panier,
                    'id_produit'  => $id_produit,
                    'id_colori'   => $couleur,
                    'id_taille'   => $taille,
                    'quantitee'   => 1,
                ]);
            } else {
                return redirect()->back()->with('error', 'Ce produit est en rupture de stock.');
            }
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

        // Récupérer le stock actuel de la variante
        $stock = Variante_produit::where('id_produit', $ligne->id_produit)
            ->where('id_taille', $ligne->id_taille)
            ->where('id_colori', $ligne->id_colori)
            ->value('quantitee_stock');

        if ($action === 'plus') {
            if ($ligne->quantitee < $stock) {
                $ligne->increment('quantitee');
            } else {
                return redirect()->back()->with('error', 'Vous avez atteint la quantité maximale disponible en stock.');
            }
        } elseif ($action === 'minus') {
            if ($ligne->quantitee > 1) {
                $ligne->decrement('quantitee');
            } else {
                $ligne->delete();
                return redirect()->back()->with('success', 'Article supprimé du panier.');
            }
        }

        return redirect()->back();
    }
}
