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
        // On charge :
        // 1. Les commentaires + leurs auteurs
        // 2. Les réponses (Niveau 1) + leurs auteurs
        // 3. Les réponses aux réponses (Niveau 2) + leurs auteurs
        $blog = Blog::with([
                    'commentaires.user', 
                    'commentaires.replies.user', 
                    'commentaires.replies.replies.user'
                ])
                ->where('idblog', $id)
                ->firstOrFail();
                    
        return view('blog.show', compact('blog'));
    }

    
    // Enregistrement d'un commentaire avec FILTRE ANTI-INSULTES
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'contenu' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer'
        ]);

        // 1. Liste des mots interdits (Tu peux en ajouter)
        $insultes = ['merde', 'con', 'connard', 'putain', 'salaud', 'idiot', 'batard', 'encule', 'fdp', 'tg'];
        
        // 2. On nettoie le texte pour éviter les majuscules/minuscules
        $contenuUser = strtolower($request->contenu);

        // 3. On vérifie chaque insulte
        foreach ($insultes as $mot) {
            // Si le mot est trouvé dans la phrase
            // "stripos" cherche le mot sans se soucier des majuscules
            if (str_contains($contenuUser, $mot)) {
                // ERREUR : On renvoie l'utilisateur en arrière avec un message d'erreur
                return back()->with('error', 'Votre message contient des termes inappropriés ("' . $mot . '"). Soyez respectueux.');
            }
        }

        // 4. Si tout est OK, on enregistre (Comme avant)
        $blog = Blog::findOrFail($id);

        Commentaire::create([
            'contenu' => $request->contenu,
            'id_user_connecte' => Auth::user()->id_user_connecte,
            'idblog' => $blog->idblog,
            'id_publication' => $blog->id_publication,
            'parent_id' => $request->parent_id
        ]);

        return back()->with('success', 'Message publié avec succès !');
    }

    // Supprimer un commentaire
    public function destroyComment($id)
    {
        // On cherche le commentaire
        $commentaire = Commentaire::findOrFail($id);

        // SÉCURITÉ : On vérifie que l'utilisateur connecté est bien l'auteur
        if ($commentaire->id_user_connecte !== Auth::user()->id_user_connecte) {
            return back()->with('error', 'Vous n\'avez pas le droit de supprimer ce message.');
        }

        // On supprime
        $commentaire->delete();

        return back()->with('success', 'Commentaire supprimé.');
    }
}