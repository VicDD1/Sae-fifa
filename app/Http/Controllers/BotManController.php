<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $userMessage = $request->input('message');

            if (empty($userMessage)) {
                return response()->json(['reply' => "Bonjour ! Comment puis-je vous aider ?"]);
            }

            // --- 1. CARTE DE NAVIGATION ---
            $siteMap = [
                ['nom' => 'Fifa Store', 'url' => url('/produits')],
                ['nom' => 'Vote', 'url' => route('vote.page')],
                ['nom' => 'Les joueurs', 'url' => url('/players')],
                ['nom' => 'Les Articles (Blog)', 'url' => '#'], // En attente
                ['nom' => 'Mon Panier', 'url' => route('panier.index')],
                ['nom' => 'Connexion', 'url' => route('login')],
            ];

            $navigationText = "";
            foreach ($siteMap as $link) {
                $navigationText .= "- Onglet '{$link['nom']}' : {$link['url']}\n";
            }

            // --- 2. INSTRUCTIONS DE COMPRÉHENSION ---
            $systemInstruction = "Tu es l'assistant du site SAEFIFA. 
            
            CONTEXTE DE L'INTERFACE :
            La barre de navigation en haut contient les onglets suivants : 'Fifa Store', 'Vote', 'Les joueurs', 'Les Articles' et 'Mon Panier'.
            
            CONSIGNES DE RÉPONSE :
            1. Pour chaque demande, indique à l'utilisateur qu'il peut soit cliquer sur l'onglet correspondant dans le menu en haut, soit utiliser le lien direct que tu fournis.
            2. Si l'utilisateur demande 'Les Articles', précise que cette section (blog sur les joueurs) est en cours de développement et n'est pas encore fonctionnelle.
            3. Utilise TOUJOURS ce format HTML pour les liens : <a href='URL'>NOM DU LIEN</a>. Pas de target='_blank'.
            
            LIENS ET ONGLETS :
            $navigationText
            
            MESSAGE UTILISATEUR : $userMessage";

            // --- 3. APPEL API ---
            $client = \Gemini::client(env('GEMINI_API_KEY'));
            
            $result = $client->generativeModel(model: 'gemini-2.5-flash')
                             ->generateContent($systemInstruction);

            return response()->json([
                'reply' => $result->text()
            ]);

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            
            // Gestion de la surcharge ou du quota
            if (strpos($msg, 'overloaded') !== false || strpos($msg, '503') !== false) {
                return response()->json(['reply' => "Je suis un peu fatigué, peux-tu répéter dans 5 secondes ?"]);
            }

            Log::error("Bot Error: " . $msg);
            return response()->json(['reply' => "Désolé, j'ai rencontré une petite erreur technique."], 200);
        }
    }
}