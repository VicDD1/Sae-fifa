<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Cet import doit correspondre exactement au package installé
use Gemini\Laravel\Facades\Gemini; 

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $userMessage = $request->input('message');
            
            // On initialise le client avec ta clé
            $client = \Gemini::client(env('GEMINI_API_KEY'));
    
            // On utilise EXACTEMENT l'un des noms de ta liste
            // 'gemini-2.5-flash' est le meilleur choix ici
            $result = $client->generativeModel(model: 'gemini-2.5-flash')->generateContent($userMessage);
    
            return response()->json([
                'reply' => $result->text()
            ]);
    
        } catch (\Exception $e) {
            // En cas de pépin, on tente la version 2.0 qui est très stable
            try {
                $client = \Gemini::client(env('GEMINI_API_KEY'));
                $result = $client->generativeModel(model: 'gemini-2.0-flash')->generateContent($userMessage);
                return response()->json(['reply' => $result->text()]);
            } catch (\Exception $e2) {
                return response()->json([
                    'reply' => "Erreur avec Gemini 2.5 : " . $e2->getMessage()
                ], 500);
            }
        }
    }
}