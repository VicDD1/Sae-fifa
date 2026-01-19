<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Blog;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Récupérer 8 produits récents avec cache (10 minutes)
        $produits = Cache::remember('home_produits', 600, function () {
            return Produit::with('photo')
                ->whereNotNull('prix_base')
                ->orderBy('id_produit', 'desc')
                ->take(8)
                ->get();
        });

        // Récupérer 3 articles récents avec cache (10 minutes)
        $articles = Cache::remember('home_articles', 600, function () {
            return Blog::orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        });

        return view('welcome', compact('produits', 'articles'));
    }
}
