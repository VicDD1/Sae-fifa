<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Nation;
use App\Models\Taille;
use App\Models\Colori;
use App\Models\Categorie_Produit;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. CHARGEMENT DES DONNÉES POUR LES LISTES DÉROULANTES ---

        // NATIONS (inchangé)
        $nations = Nation::whereIn('id_nation', function ($query) {
            $query->select('id_nation')->from('produit')->whereNotNull('id_nation');
        })->orderBy('nom_nation')->get();

        // CATEGORIES (Parents uniquement : ceux qui n'ont pas de parent défini dans 'sous_categorie')
        $categories = Categorie_Produit::whereNull('sous_categorie')
            ->orderBy('label_categorie')
            ->get();

        // TAILLES (inchangé)
        $tailles = Taille::whereIn('id_taille', function ($query) {
            $query->select('id_taille')->from('variante_produit')->whereNotNull('id_taille');
        })->orderBy('label_taille')->get();

        // COULEURS (inchangé)
        $couleurs = Colori::orderBy('id_colori')->get();


        // On ne charge les sous-catégories que si une catégorie parent est sélectionnée
        $sous_categories = collect(); // On démarre avec une collection vide

        if ($request->filled('id_categorie')) {
            // C'est ici que l'on applique votre logique SQL :
            $sous_categories = Categorie_Produit::where('sous_categorie', $request->id_categorie)
                ->orderBy('label_categorie')
                ->get();
        }


        // --- 2. REQUÊTE PRODUITS ---
        
        $query = Produit::query();
        $query->select('produit.*')->distinct();

        // RECHERCHE TEXTUELLE
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('label_produit', 'ILIKE', $searchTerm)
                ->orWhere('description_produit', 'ILIKE', $searchTerm);
            });
        }

        if ($request->filled('id_nation')) {
            $query->where('produit.id_nation', $request->id_nation);
        }

        if ($request->filled('id_categorie')) {
            
            if (!$request->filled('sous_categorie')) {
                $idsEnfants = Categorie_Produit::where('sous_categorie', $request->id_categorie)
                    ->pluck('id_categorie') // On ne prend que la colonne ID
                    ->toArray();
                $tousLesIds = array_merge([$request->id_categorie], $idsEnfants);
                $query->whereIn('produit.id_categorie', $tousLesIds);
            }
        }

        // FILTRE SOUS-CATEGORIE (Enfant)
        if ($request->filled('sous_categorie')) {
            $query->where('produit.id_categorie', $request->sous_categorie);
        }

        // FILTRE PRIX
        if ($request->filled('max_price')) {
            $query->where('produit.prix_base', '<=', $request->max_price);
        }

        // FILTRE TAILLE
        if ($request->filled('id_taille')) {
            $query->join('variante_produit as vp_taille', 'produit.id_produit', '=', 'vp_taille.id_produit')
                ->where('vp_taille.id_taille', $request->id_taille);
        }

        // FILTRE COULEUR
        if ($request->filled('id_colori')) {
            $query->join('variante_produit as vp_colori', 'produit.id_produit', '=', 'vp_colori.id_produit')
                ->where('vp_colori.id_colori', $request->id_colori);
        }

        // TRI
        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('produit.prix_base', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('produit.prix_base', 'desc');
            }
        }

        $products = $query->get();

        return view('products', compact('products', 'nations', 'tailles', 'couleurs', 'categories', 'sous_categories'));
    }
    public function detail($id)
    {
        // Charger le produit demandé
        $product = Produit::with(['couleurs', 'tailles'])->findOrFail($id);

        // Trouver des produits similaires :
        // même catégorie ou même sous-catégorie, mais exclure le produit lui-même
        $similarProducts = Produit::where(function ($q) use ($product) {
                $q->where('id_categorie', $product->id_categorie);
            })
            ->where('id_produit', '!=', $product->id_produit)
            ->limit(10)
            ->get();

        return view('product_detail', compact('product', 'similarProducts'));
    }


}