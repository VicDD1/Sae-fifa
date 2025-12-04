<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Categorie_Produit;

class Categorie_ProduitControler extends Controller
{
    public function index(){
    	return view ("categorie_produit-list", ['categorie_produits'=>Categorie_Produit::all() ]);
    }
}
