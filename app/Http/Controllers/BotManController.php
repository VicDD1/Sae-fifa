<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
        $botman = BotManFactory::create([]);

        $currentUrl = $request->input('current_url');

        $botman->hears('.*(accueil|home|début).*', function (BotMan $bot) use ($currentUrl) {
            if ($currentUrl == '/') {
                $bot->reply("Vous êtes déjà sur la page d'Accueil (1er onglet à gauche).");
            } else {
                $bot->reply("Pour revenir au début, veuillez soit cliquer sur l'onglet Accueil en 1ère position ou cliquer sur ce lien :<br><a href='/'>🏠 Accueil</a>");
            }
        });

        $botman->hears('.*(fifa store|store|boutique|produit|acheter).*', function (BotMan $bot) use ($currentUrl) {
            if ($currentUrl == '/produits') {
                $bot->reply("Vous parcourez actuellement le Fifa Store.");
            } else {
                $bot->reply("Pour accéder au Fifa Store, veuillez soit cliquer sur le bouton Fifa Store en 2ème position ou cliquer sur ce lien :<br><a href='/produits'>🛍️ Aller au Store</a>");
            }
            
            if (Auth::check() && Auth::user()->professionnel) {
                $bot->reply("En tant que pro, vous pouvez proposer un produit via l'onglet Faire une demande de produit situé tout à droite.");
            }
        });

        $botman->hears('.*(commande|achat|mes commandes).*', function (BotMan $bot) {
            if (Auth::check()) {
                $bot->reply("Pour voir vos achats, veuillez soit cliquer sur le bouton bleu Mes commandes à droite de votre nom ou cliquer sur ce lien :<br><a href='/mes_commandes'>📦 Mes commandes</a>");
            } else {
                $bot->reply("Vous devez être connecté pour voir vos commandes. Cliquez sur l'icône de profil à droite.");
            }
        });

        $botman->hears('.*(profil|compte|modifier|paramètre).*', function (BotMan $bot) {
            if (Auth::check()) {
                $bot->reply("Pour modifier vos informations, veuillez soit cliquer sur votre nom ( " . Auth::user()->name . " ) dans la barre bleue ou cliquer sur ce lien :<br><a href='/mon-profil'>👤 Modifier mon compte</a>");
            } else {
                $bot->reply("Veuillez vous connecter pour accéder à votre profil.");
            }
        });

        $botman->hears('.*(pro|professionnel|faire une demande|proposer).*', function (BotMan $bot) {
            if (Auth::check() && Auth::user()->professionnel) {
                $bot->reply("Pour ajouter un article, cliquez sur l'onglet Faire une demande de produit situé à l'extrémité droite de la barre de navigation ou ici :<br><a href='/proposer_un_produit'>💡 Faire une demande</a>");
            } else {
                $bot->reply("La proposition de produit est réservée aux comptes professionnels.");
            }
        });

        $botman->hears('.*(vote|voter).*', function (BotMan $bot) use ($currentUrl) {
            if ($currentUrl == '/vote/fifa') {
                $bot->reply("Vous êtes sur la page de Vote (3ème onglet).");
            } else {
                $bot->reply("Pour voter, veuillez soit cliquer sur l'onglet Vote en 3ème position ou cliquer sur ce lien :<br><a href='/vote/fifa'>⚽ Voter ici</a>");
            }
        });

        $botman->hears('.*(joueur|les joueurs|players).*', function (BotMan $bot) use ($currentUrl) {
            if ($currentUrl == '/players') {
                $bot->reply("Vous consultez la liste Les joueurs (4ème onglet).");
            } else {
                $bot->reply("Pour voir l'effectif, veuillez soit cliquer sur l'onglet Les joueurs en 4ème position ou cliquer sur ce lien :<br><a href='/players'>🏃 Voir les joueurs</a>");
            }
        });

        $botman->hears('.*(article|actualité|news).*', function (BotMan $bot) {
            $bot->reply("L'onglet Les Articles (5ème position) est en cours de préparation.");
        });

        $botman->hears('.*(panier|mon panier).*', function (BotMan $bot) {
            $bot->reply("Pour accéder au panier, veuillez soit cliquer sur l'onglet Mon Panier en 6ème position ou cliquer sur ce lien :<br><a href='/panier'>🛒 Voir mon Panier</a>");
        });

        $botman->hears('.*(connexion|connecter|login|quitter|déconnexion).*', function (BotMan $bot) {
            if (Auth::check()) {
                $bot->reply("Bonjour " . Auth::user()->name . " ! Vous êtes connecté.");
                $bot->reply("Pour quitter, cliquez sur l'icône Power rouge à droite ou ici :<br><a href='/logout'>🚪 Se déconnecter</a>");
            } else {
                $bot->reply("Pour vous connecter, veuillez cliquer sur l'icône de profil grise à droite ou ici :<br><a href='/se_connecter'>🔑 Se connecter</a>");
            }
        });

        $botman->hears('.*aide.*', function (BotMan $bot) {
            $bot->reply("Voici l'ordre de votre navigation :<br>1. Accueil<br>2. Fifa Store<br>3. Vote<br>4. Les joueurs<br>5. Les Articles<br>6. Mon Panier");
            if (Auth::check()) {
                $bot->reply("Options compte : Mes commandes, Modifier profil.");
            }
        });

        $botman->fallback(function ($bot) {
            $bot->reply("Désolé, je n'ai pas compris. Tapez aide pour voir le guide.");
        });

        $botman->listen();
    }
}