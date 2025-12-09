<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vote;
use App\Models\Joueur;

class VoteController extends Controller
{
    /**
     * Afficher les systèmes de vote
     */
    public function index()
    {
        $systems = Vote::all(); // À adapter si tu utilises encore VoteSystem
        return view('vote.index', compact('systems'));
    }

    /**
     * Afficher les joueurs d'un système de vote
     */
    public function show(Vote $system)
    {
        $players = $system->players;

        return view('vote.show', compact('system', 'players'));
    }

    /**
     * Page d'affichage du formulaire final pour valider un vote
     */
    public function votePage()
    {
        // On récupère les thèmes dans la table theme_vote
        $themes = Vote::all();

        // On récupère les joueurs
        $players = Joueur::all();

        // Classements
        $rankings = [
            '#1 - Expert',
            '#2 - Pro',
            '#3 - Intermédiaire',
            '#4 - Débutant'
        ];

        return view('vote_fifa', compact('themes', 'players', 'rankings'));
    }

    /**
     * Traitement du formulaire de vote
     */
    public function submit(Request $request)
    {
        $request->validate([
            'theme'   => 'required',
            'player'  => 'required',
            'ranking' => 'required'
        ]);

        return redirect()->back()->with('success', 'Votre vote a bien été enregistré !');
    }
}
