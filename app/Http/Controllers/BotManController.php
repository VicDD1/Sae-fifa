<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class BotManController extends Controller
{
    /**
     * Gère les messages du chatbot SAEFIFA.
     * Utilise le modèle Gemini 2.5 Pro pour une navigation intelligente.
     */
    public function handle(Request $request)
    {
        try {
            // Récupération du message de l'utilisateur
            $userMessage = $request->input('message');

            if (empty($userMessage)) {
                return response()->json(['reply' => "Bonjour ! Je suis l'assistant SAEFIFA. Comment puis-je vous aider ?"]);
            }

            // --- 1. CONFIGURATION DE LA NAVIGATION (Sitemap) ---
            // On définit les routes pour que le bot puisse diriger l'utilisateur
            $siteMap = [
                ['nom' => 'Accueil', 'url' => url('/')],
                ['nom' => 'Boutique / Produits', 'url' => url('/produits')],
                ['nom' => 'Liste des Joueurs', 'url' => url('/players')],
                ['nom' => 'Page de Vote FIFA', 'url' => route('vote.page')],
                ['nom' => 'Mon Panier', 'url' => route('panier.index')],
                ['nom' => 'Connexion', 'url' => route('login')],
                ['nom' => 'Inscription Particulier', 'url' => route('register.step1')],
                ['nom' => 'Inscription Professionnel', 'url' => route('registerPro.step1')],
                ['nom' => 'Mes Commandes', 'url' => route('commande.liste')],
                ['nom' => 'Vendre un produit', 'url' => route('registerProduct.step1')],
            ];

            $navigationText = "Voici les liens de navigation du site :\n";
            foreach ($siteMap as $link) {
                $navigationText .= "- {$link['nom']} : {$link['url']}\n";
            }

            // --- 2. INSTRUCTIONS POUR L'IA (Prompt) ---
            $systemInstruction = "Tu es l'assistant de navigation du site SAEFIFA.
            
            TON RÔLE :
            - Guider l'utilisateur vers la bonne page en utilisant les liens fournis.
            - Toujours donner l'URL complète et exacte.
            - Si l'utilisateur cherche des produits, propose la Boutique.
            - Si l'utilisateur parle de joueurs ou de vote, propose les liens correspondants.
            
            CONSIGNES :
            - Sois très poli et concis.
            - N'invente jamais d'URL qui n'est pas dans la liste.
            
            LISTE DES LIENS :
            $navigationText
            
            QUESTION : $userMessage";

            // --- 3. APPEL À GEMINI 2.5 PRO ---
            $client = \Gemini::client(env('GEMINI_API_KEY'));
            
            // On utilise le modèle 2.5 Pro pour plus de stabilité
            $result = $client->generativeModel(model: 'gemini-2.5-pro')
                             ->generateContent($systemInstruction);

            return response()->json([
                'reply' => $result->text()
            ]);

        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error("Erreur BotMan : " . $error);

            // Gestion personnalisée des erreurs de surcharge (Overloaded)
            if (strpos($error, 'overloaded') !== false || strpos($error, '503') !== false) {
                return response()->json([
                    'reply' => "Je suis un peu fatigué, peux-tu répéter dans 5 secondes ?"
                ]);
            }

            // Message générique pour les autres erreurs techniques
            return response()->json([
                'reply' => "Désolé, j'ai rencontré une petite erreur technique. Réessayez dans un instant."
            ], 200); 
        }
    }
}