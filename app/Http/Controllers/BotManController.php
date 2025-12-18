<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

class BotManController extends Controller
{
    public function handle()
    {
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

        $botman = BotManFactory::create([]);

        // Commande "aide"
        $botman->hears('.*aide.*', function (BotMan $bot) {
            $bot->reply("Voici les accès rapides (cliquez pour naviguer) :");
            
            // Liste complète des liens
            $bot->reply("🏠 [Accueil](/)");
            $bot->reply("📂 [Nos Services](/services)");
            $bot->reply("💳 [Tarifs](/pricing)");
            $bot->reply("👤 [Mon Profil](/profile)");
            $bot->reply("📞 [Contact](/contact)");
            $bot->reply("❓ [FAQ](/faq)");
        });

        // Cas par défaut
        $botman->fallback(function ($bot) {
            $bot->reply("Désolé, je n'ai pas compris. Tapez **aide**.");
        });

        $botman->listen();
    }
}