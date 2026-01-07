<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Categorie_Produit;

class Categorie_ProduitControler extends Controller
{
    public function index(){
    	return view ("categorie_produit-list", ['categorie_produits'=>Categorie_Produit::all() ]);
    }
    // 1. Afficher le formulaire
    public function create()
    {
        // On va créer ce fichier vue à l'étape 5
        return view('create_categorie_produit');
    }

    // 2. Enregistrer la catégorie
    public function store(Request $request)
    {
        // Validation : le nom est obligatoire
        $request->validate([
            'label_categorie' => 'required|string|max:255'
        ]);

        // Création dans la table 'categorie_produit' via le Modèle
        Categorie_Produit::create([
            'label_categorie' => $request->label_categorie
        ]);

        // Redirection avec message de succès
        return redirect()->back()->with('success', 'Catégorie créée avec succès !');
    }
}
