<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini;
use App\Models\Joueur;
use App\Models\Competition;
use App\Models\Article;

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        $queryText = $request->input('queryResult.queryText');
        $apiKey = env('GEMINI_API_KEY');
        $client = Gemini::client($apiKey);

        // 1. EXTRACTION DES DONNÉES DE TA BASE (Exemple avec Joueurs)
        // On récupère les données brutes pour donner du contexte à l'IA
        $donneesLocales = Joueur::where('nom', 'like', "%$queryText%")->get()->toJson();
        $articlesRelatifs = Article::where('titre', 'like', "%$queryText%")->limit(2)->get()->toJson();

        // 2. CONSTRUCTION DU PROMPT (Instruction pour Gemini)
        $prompt = "Tu es l'assistant officiel du site FIFA. 
        Voici des données extraites de notre base de données : $donneesLocales et $articlesRelatifs.
        L'utilisateur demande : '$queryText'.
        Réponds de manière naturelle en utilisant ces données. Si tu ne trouves pas la réponse dans les données fournies, utilise tes connaissances générales sur le football pour l'aider, mais précise-le.";

        // 3. APPEL À GEMINI
        $result = $client->geminiPro()->generateContent($prompt);
        $reponseIA = $result->text();

        // 4. RETOUR À DIALOGFLOW
        return response()->json([
            'fulfillmentText' => $reponseIA
        ]);
    }
}
