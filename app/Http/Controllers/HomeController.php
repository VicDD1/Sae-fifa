<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Blog;

class HomeController extends Controller
{
    public function index()
    {
        // Récupérer 8 produits récents avec leur photo
        $produits = Produit::with('photo')
            ->whereNotNull('prix_base')
            ->orderBy('id_produit', 'desc')
            ->take(8)
            ->get();

        // Récupérer 3 articles récents du blog
        $articles = Blog::orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('welcome', compact('produits', 'articles'));
    }
}
