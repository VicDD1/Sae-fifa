<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Vote;
use App\Models\Joueur;

class VoteController extends Controller
{
    /**
     * Affiche le formulaire de vote
     */
    public function votePage()
    {
        // Vérifier si l'utilisateur a déjà voté
        if (auth()->check()) {
            $existe = DB::table('voter')
                ->where('id_user', auth()->id())
                ->first();

            if ($existe) {
                return back()->with(
                    'erreur_vote',
                    "T’as déjà un vote le sang, casse-toi à l’alim me chercher un flashon 😭"
                );
            }
        }

        $themes = Vote::all();
        $joueurs = Joueur::all();

        return view('vote_fifa', compact('themes', 'joueurs'));
    }

    /**
     * Soumission du vote
     */
    public function submit(Request $request)
    {
        // Empêcher un 2ᵉ vote
        $existe = DB::table('voter')
            ->where('id_user', auth()->id())
            ->first();

        if ($existe) {
            return redirect()->route('vote.page')->with(
                'erreur_vote',
                "T’as déjà un vote le sang, casse-toi à l’alim me chercher un flashon 😭"
            );
        }

        // Validation
        $request->validate([
            'theme'       => 'required',

            'joueur1'     => 'required|different:joueur2,joueur3,joueur4',
            'joueur2'     => 'required|different:joueur1,joueur3,joueur4',
            'joueur3'     => 'required|different:joueur1,joueur2,joueur4',
            'joueur4'     => 'required|different:joueur1,joueur2,joueur3',

            'classement1' => 'required|different:classement2,classement3,classement4',
            'classement2' => 'required|different:classement1,classement3,classement4',
            'classement3' => 'required|different:classement1,classement2,classement4',
            'classement4' => 'required|different:classement1,classement2,classement3',
        ]);

        // 1️⃣ Créer un vote dans table vote
        $idVote = DB::table('vote')->insertGetId([
            'id_theme' => $request->theme,
            'created_at' => now()
        ]);

        // 2️⃣ Enregistrer les joueurs + classement
        DB::table('joueur_vote')->insert([
            [
                'id_vote' => $idVote,
                'id_joueur' => $request->joueur1,
                'classement' => $request->classement1,
            ],
            [
                'id_vote' => $idVote,
                'id_joueur' => $request->joueur2,
                'classement' => $request->classement2,
            ],
            [
                'id_vote' => $idVote,
                'id_joueur' => $request->joueur3,
                'classement' => $request->classement3,
            ],
            [
                'id_vote' => $idVote,
                'id_joueur' => $request->joueur4,
                'classement' => $request->classement4,
            ],
        ]);

        // 3️⃣ Associer le vote à l'utilisateur connecté
        DB::table('voter')->insert([
            'id_user' => auth()->id(),
            'id_vote' => $idVote
        ]);

        // Préparer le récap
        $theme = Vote::where('id_theme', $request->theme)->first();

        $recap = [
            'theme' => $theme,
            'votes' => [
                ['joueur' => Joueur::find($request->joueur1), 'classement' => $request->classement1],
                ['joueur' => Joueur::find($request->joueur2), 'classement' => $request->classement2],
                ['joueur' => Joueur::find($request->joueur3), 'classement' => $request->classement3],
                ['joueur' => Joueur::find($request->joueur4), 'classement' => $request->classement4],
            ]
        ];

        return view('vote_recapitulatif', compact('recap'));
    }
}
