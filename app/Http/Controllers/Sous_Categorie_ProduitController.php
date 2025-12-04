<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Sous_Categorie_Produit;

class Sous_Categorie_ProduitControler extends Controller
{
    public function index(){
    	return view ("sous_categorie_produit-list", ['sous_categorie_produits'=>Sous_Categorie_Produit::all() ]);
    }
}