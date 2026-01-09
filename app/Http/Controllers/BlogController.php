<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    // Page liste des articles
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->get();
        return view('blog.index', compact('blogs'));
    }

    // Page détail d'un article
    public function show($id)
    {
        // On cherche le blog par son ID
        // On charge aussi les commentaires, leurs réponses, et les utilisateurs associés pour éviter de faire 50 requêtes SQL
        $blog = Blog::with(['commentaires.replies.user', 'commentaires.user'])
                    ->where('idblog', $id)
                    ->firstOrFail();
                    
        return view('blog.show', compact('blog'));
    }

    // Enregistrement d'un commentaire (User Story 1 & 2)
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'contenu' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer' // Sert si c'est une réponse
        ]);

        // 1. On récupère l'article pour connaitre son ID_PUBLICATION (obligatoire pour ta base)
        $blog = Blog::findOrFail($id);

        // 2. On crée le commentaire
        Commentaire::create([
            'contenu' => $request->contenu,
            'id_user_connecte' => Auth::user()->id_user_connecte, // L'ID de celui qui est connecté
            'idblog' => $blog->idblog,            // L'ID du blog
            'id_publication' => $blog->id_publication, // L'ID publication (Crucial pour ta clé étrangère composite)
            'parent_id' => $request->parent_id    // NULL si c'est un avis, ID si c'est une réponse
        ]);

        return back()->with('success', 'Message publié avec succès !');
    }
}