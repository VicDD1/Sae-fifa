<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Theme_vote;
use App\Models\Joueur;
use App\Models\Joueur_theme;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Theme_VoteController extends Controller
{
    /**
     * Affiche la liste des thèmes de vote
     */
    public function index()
    {
        $themes = Theme_vote::withCount('joueurs')->get();
        
        return view('votation.themes_index', compact('themes'));
    }

    /**
     * Affiche le formulaire de création d'un thème
     */
    public function create()
    {
        // Vérification que l'utilisateur est du service vente (IDs 11, 12, 13)
        if (!auth()->check() || !in_array(auth()->user()->id_user_connecte, [11, 12, 13])) {
            abort(403, 'Accès réservé au service vente.');
        }

        $joueurs = Joueur::orderBy('nom')->get();
        
        return view('votation.create', compact('joueurs'));
    }

    /**
     * User Story 1 : Créer un nouveau thème de vote
     * 
     * Règles :
     * - titre (nom_theme) obligatoire
     * - date_fin obligatoire
     * - Si date_fin est dans le passé, refuser la création
     * - Un thème peut être créé vide (sans joueur) ou avec des joueurs
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_theme' => 'required|string|max:255',
            'date_fin_vote' => 'required|date',
            'joueurs' => 'nullable|array',
            'joueurs.*' => 'exists:joueur,id_joueur',
        ], [
            'nom_theme.required' => 'Le titre du thème est obligatoire.',
            'date_fin_vote.required' => 'La date de fin est obligatoire.',
            'date_fin_vote.date' => 'La date de fin doit être une date valide.',
        ]);

        // Vérifier que la date de fin n'est pas dans le passé
        $dateFin = Carbon::parse($request->date_fin_vote)->startOfDay();
        $aujourdhui = Carbon::now()->startOfDay();

        if ($dateFin < $aujourdhui) {
            return back()
                ->withInput()
                ->withErrors(['date_fin_vote' => 'La date de fin ne peut pas être dans le passé.']);
        }

        // Créer le thème
        $maxId = DB::table('theme_vote')->max('id_theme') ?? 0;
        
        $theme = new Theme_vote();
        $theme->id_theme = $maxId + 1;
        $theme->nom_theme = $request->nom_theme;
        $theme->date_fin_vote = $dateFin;
        $theme->save();

        // Associer les joueurs si fournis (User Story 2 intégrée)
        if ($request->has('joueurs') && is_array($request->joueurs)) {
            foreach ($request->joueurs as $idJoueur) {
                DB::table('joueur_theme')->insert([
                    'id_theme' => $theme->id_theme,
                    'id_joueur' => $idJoueur,
                ]);
            }
        }

        return redirect()
            ->route('theme_vote.index')
            ->with('success', 'Le thème de vote "' . $theme->nom_theme . '" a été créé avec succès.');
    }

    /**
     * Affiche un thème et ses joueurs associés
     */
    public function show($id)
    {
        $theme = Theme_vote::findOrFail($id);
        $joueursAssocies = $theme->joueurs()->orderBy('nom')->get();
        $joueursDisponibles = Joueur::whereNotIn('id_joueur', $joueursAssocies->pluck('id_joueur'))
            ->orderBy('nom')
            ->get();

        return view('votation.theme_show', compact('theme', 'joueursAssocies', 'joueursDisponibles'));
    }

    /**
     * User Story 2 : Associer des joueurs à un thème existant
     * 
     * Règles :
     * - On associe des joueurs à un thème existant
     * - Interdire les doublons : un même joueur ne peut pas être ajouté deux fois au même thème
     */
    public function associerJoueurs(Request $request, $id)
    {
        $theme = Theme_vote::findOrFail($id);

        $request->validate([
            'joueurs' => 'required|array|min:1',
            'joueurs.*' => 'exists:joueur,id_joueur',
        ], [
            'joueurs.required' => 'Veuillez sélectionner au moins un joueur.',
            'joueurs.min' => 'Veuillez sélectionner au moins un joueur.',
        ]);

        $joueursAjoutes = 0;
        $joueursDoublons = 0;

        foreach ($request->joueurs as $idJoueur) {
            // Vérifier si le joueur n'est pas déjà associé (éviter les doublons)
            $existe = DB::table('joueur_theme')
                ->where('id_theme', $theme->id_theme)
                ->where('id_joueur', $idJoueur)
                ->exists();

            if (!$existe) {
                DB::table('joueur_theme')->insert([
                    'id_theme' => $theme->id_theme,
                    'id_joueur' => $idJoueur,
                ]);
                $joueursAjoutes++;
            } else {
                $joueursDoublons++;
            }
        }

        $message = $joueursAjoutes . ' joueur(s) ajouté(s) au thème.';
        if ($joueursDoublons > 0) {
            $message .= ' ' . $joueursDoublons . ' joueur(s) déjà présent(s) ignoré(s).';
        }

        return redirect()
            ->route('theme_vote.show', $theme->id_theme)
            ->with('success', $message);
    }

    /**
     * Retirer un joueur d'un thème
     */
    public function retirerJoueur($idTheme, $idJoueur)
    {
        $theme = Theme_vote::findOrFail($idTheme);

        DB::table('joueur_theme')
            ->where('id_theme', $idTheme)
            ->where('id_joueur', $idJoueur)
            ->delete();

        return redirect()
            ->route('theme_vote.show', $idTheme)
            ->with('success', 'Le joueur a été retiré du thème.');
    }

    /**
     * Supprimer un thème
     */
    public function destroy($id)
    {
        $theme = Theme_vote::findOrFail($id);

        // Supprimer les associations joueur_theme
        DB::table('joueur_theme')->where('id_theme', $id)->delete();

        // Supprimer le thème
        $theme->delete();

        return redirect()
            ->route('theme_vote.index')
            ->with('success', 'Le thème a été supprimé.');
    }
}
