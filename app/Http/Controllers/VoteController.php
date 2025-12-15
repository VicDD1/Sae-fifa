<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Vote;
use App\Models\Joueur;

class VoteController extends Controller
{
    /**
     * Page du formulaire
     */
    public function votePage()
    {
        $themes = DB::table('theme_vote')->get();
        $joueurs = DB::table('joueur')->get();

        return view('vote_fifa', compact('themes', 'joueurs'));
    }


    /**
     * Traitement du vote
     */
   public function submit(Request $request)
{
    // Validation des champs
    $request->validate([
        'theme' => 'required',

        'joueur1' => 'required|different:joueur2,joueur3,joueur4',
        'joueur2' => 'required|different:joueur1,joueur3,joueur4',
        'joueur3' => 'required|different:joueur1,joueur2,joueur4',
        'joueur4' => 'required|different:joueur1,joueur2,joueur3',

        'classement1' => 'required|different:classement2,classement3,classement4',
        'classement2' => 'required|different:classement1,classement3,classement4',
        'classement3' => 'required|different:classement1,classement2,classement4',
        'classement4' => 'required|different:classement1,classement2,classement3',
    ]);

    /* ---------------------------------------------------------
        ✔ 1. Vérifier si l’utilisateur a déjà voté POUR CE THEME
    --------------------------------------------------------- */

    $aDejaVote = DB::table('voter')
        ->join('vote', 'vote.id_vote', '=', 'voter.id_vote')
        ->where('voter.id_user_connecte', auth()->id())
        ->where('vote.id_theme', $request->theme)
        ->exists();

    if ($aDejaVote) {
        return redirect()->route('vote.page')->with(
            'erreur_vote',
            "Casse-toi à l'alim me chercher un flashon digne des plus grands 🤣 (t'as déjà voté pour cette catégorie !)"
        );
    }

    /* ---------------------------------------------------------
        ✔ 2. Créer un nouvel ID_VOTE manuellement
    --------------------------------------------------------- */
    $newIdVote = DB::table('vote')->max('id_vote') + 1;

    DB::table('vote')->insert([
        'id_vote'  => $newIdVote,
        'id_theme' => $request->theme
    ]);

    /* ---------------------------------------------------------
        ✔ 3. Enregistrer les choix joueur + classement
    --------------------------------------------------------- */
    DB::table('joueur_vote')->insert([
        ['id_vote' => $newIdVote, 'id_joueur' => $request->joueur1, 'rank' => $request->classement1],
        ['id_vote' => $newIdVote, 'id_joueur' => $request->joueur2, 'rank' => $request->classement2],
        ['id_vote' => $newIdVote, 'id_joueur' => $request->joueur3, 'rank' => $request->classement3],
        ['id_vote' => $newIdVote, 'id_joueur' => $request->joueur4, 'rank' => $request->classement4],
    ]);

    /* ---------------------------------------------------------
        ✔ 4. Associer vote + utilisateur
    --------------------------------------------------------- */
    DB::table('voter')->insert([
        'id_user_connecte' => auth()->id(),
        'id_vote' => $newIdVote
    ]);

    /* ---------------------------------------------------------
        ✔ 5. Charger les données pour le récapitulatif
    --------------------------------------------------------- */

    $theme = DB::table('theme_vote')->where('id_theme', $request->theme)->first();

    $recap = [
        'theme' => $theme->nom_theme,
        'votes' => [
            ['joueur' => Joueur::find($request->joueur1), 'rank' => $request->classement1],
            ['joueur' => Joueur::find($request->joueur2), 'rank' => $request->classement2],
            ['joueur' => Joueur::find($request->joueur3), 'rank' => $request->classement3],
            ['joueur' => Joueur::find($request->joueur4), 'rank' => $request->classement4],
        ]
    ];

    return view('vote_recapitulatif', compact('recap'));
}

}

