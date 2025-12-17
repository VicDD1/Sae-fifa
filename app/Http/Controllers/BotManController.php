<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use App\Models\Product; // <--- Vérifie que c'est bien le nom de ton Modèle

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $userMessage = $request->input('message');
            
            // --- 1. CARTE DE NAVIGATION (Tes routes exactes) ---
            // On liste uniquement les pages principales du site
            $siteMap = [
                ['nom' => 'Accueil', 'url' => url('/')],
                ['nom' => 'Boutique (Tous nos produits)', 'url' => url('/produits')], // Route ligne 57
                ['nom' => 'Mon Panier', 'url' => route('panier.index')],              // Route ligne 101
                ['nom' => 'Connexion / Se connecter', 'url' => route('login')],      // Route ligne 94
                ['nom' => 'Créer un compte (Particulier)', 'url' => route('register.step1')], 
                ['nom' => 'Créer un compte (Professionnel)', 'url' => route('registerPro.step1')],
                ['nom' => 'Page de Vote FIFA', 'url' => route('vote.page')],          // Route ligne 161
                ['nom' => 'Mes Commandes', 'url' => route('commande.liste')],        // Route ligne 168
                ['nom' => 'Proposer ou créer un produit', 'url' => route('registerProduct.step1')],
            ];
    
            $navigationText = "Voici les destinations disponibles sur le site :\n";
            foreach ($siteMap as $link) {
                $navigationText .= "- " . $link['nom'] . " : " . $link['url'] . "\n";
            }
    
            // --- 2. INSTRUCTIONS STRICTES ---
            $systemInstruction = "Tu es l'assistant de navigation du site SAEFIFA.
            Ton seul rôle est de dire à l'utilisateur sur quel lien cliquer pour accéder à une page.
            
            RÈGLES :
            1. NE RECOMMANDE PAS de produits spécifiques. 
            2. Si l'utilisateur cherche des produits, envoie-le vers la page 'Boutique'.
            3. Donne TOUJOURS l'URL complète et cliquable.
            4. Si la demande ne concerne pas une page du site, réponds poliment que tu ne peux que l'aider à naviguer.
            
            $navigationText
            
            Demande de l'utilisateur : " . $userMessage;
    
            // --- 3. APPEL À GEMINI ---
            $client = \Gemini::client(env('GEMINI_API_KEY'));
            
            // On utilise le modèle Gemini 2.5 Flash
            $result = $client->generativeModel(model: 'gemini-2.5-flash')
                             ->generateContent($systemInstruction);
    
            return response()->json([
                'reply' => $result->text()
            ]);
    
        } catch (\Exception $e) {
            return response()->json(['reply' => "Erreur : " . $e->getMessage()], 500);
        }
    }
}