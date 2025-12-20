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
        // --- REMPLACEMENT DU BLOC D'EXTRACTION ---
        $combinedId = $request->input('userId');
        $currentUrl = str_contains($combinedId, '|') ? explode('|', $combinedId)[1] : '/';
        // ------------------------------------------   
        // On s'assure que l'URL commence par un slash pour tes tests if
        $currentUrl = '/' . ltrim($currentUrl, '/');    
        // Garde ton debug pour confirmer que tout est OK
        $botman->hears('debug url', function (BotMan $bot) use ($currentUrl) {
            $bot->reply("URL extraite avec succès : " . $currentUrl);
        });

        // --- 1. FONCTION D'ACHAT (SÉCURISÉE ET NETTOYÉE) ---
        $botman->hears('.*(acheter|chercher|trouver|vouloir|besoin|aimerais) (.*)', function (BotMan $bot, $action, $phraseSaisie) {
            if ($currentUrl == '/produits'){
                $phraseSaisie = mb_strtolower(trim($phraseSaisie));
                $separateurs = [' pour ', ' car ', ' afin ', ' parce ', ' quand '];
                foreach ($separateurs as $sep) {
                    if (str_contains($phraseSaisie, $sep)) {
                        $parties = explode($sep, $phraseSaisie);
                        $phraseSaisie = $parties[0]; 
                    }
                }
                $motsAIgnorer = ['un', 'une', 'le', 'la', 'les', 'des', 'du', 'de', 'l\'', 'd\'', 'mon', 'ma', 'mes', 'votre', 'vos'];
                $mots = explode(' ', $phraseSaisie);
                $motsUtiles = array_filter($mots, function($mot) use ($motsAIgnorer) {
                    return !in_array($mot, $motsAIgnorer) && strlen($mot) > 1;
                });
                $produitNettoye = !empty($motsUtiles) ? implode(' ', $motsUtiles) : $phraseSaisie;

                $bot->reply("C'est noté ! Pour trouver " . $produitNettoye . " sur notre boutique, voici la marche à suivre :");
                $bot->reply("1. Dans la barre de recherche en haut, tapez simplement : " . $produitNettoye . ".");
                $bot->reply("2. Cliquez directement sur l'image ou le nom de l'article pour accéder à sa fiche.");
                if (Auth::check()) {
                    $bot->reply("4. Sur la fiche produit, choisissez votre taille et votre couleur, puis cliquez sur Ajouter au panier.");
                } else {
                    $bot->reply("4. Attention : Vous devez être connecté pour ajouter au panier. Une fois connecté, vous pourrez choisir la taille et la couleur.");
                    $bot->reply("👉 <a href='/se_connecter'>🔑 Se connecter ici</a>");
                }
            }
            else{
                $bot->reply("👉 Rendez-vous dans le Fifa Store (2ème onglet) <a href='/produits'>🛍️ Aller au Fifa Store</a> puis redemandez pour votre article dans le format :  j'aimerais acheter un(e) (PRODUIT).");
            }
        });

        // --- 2. NAVIGATION (TES VERSIONS LONGUES RESTAURÉES) ---
        $botman->hears('.*(accueil|home|début).*', function (BotMan $bot) use ($currentUrl) {
            if ( $currentUrl == '/') {
                $bot->reply("Vous êtes déjà sur la page d'Accueil (1er onglet à gauche).");
            } else {
                $bot->reply("Pour revenir au début, veuillez soit cliquer sur l'onglet Accueil en 1ère position ou cliquer sur ce lien :<br><a href='/'>🏠 Accueil</a>");
            }
        });

        $botman->hears('.*(fifa store|store|boutique|produits).*', function (BotMan $bot) use ($currentUrl) {
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
                if ($currentUrl == '/mes_commande')
                {
                    $bot->reply("Vous êtes déjà sur vos commandes");
                }
                else{
                    $bot->reply("Pour voir vos commande, veuillez soit cliquer sur le bouton bleu Mes commandes à droite de votre nom ou cliquer sur ce lien :<br><a href='/mes_commandes'>📦 Mes commandes</a>");
                }
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

        $botman->hears('.*(professionnel|faire une demande|proposer).*', function (BotMan $bot) {
            if (Auth::check() && Auth::user()->professionnel) {
                $bot->reply("Pour ajouter un article, cliquez sur l'onglet Faire une demande de produit situé à l'extrémité droite de la barre de navigation ou ici :<br><a href='/proposer_un_produit'>💡 Faire une demande</a>");
            } else {
                $bot->reply("La proposition de produit est réservée aux comptes professionnels.");
            }
        });

        $botman->hears('.*(vote|voter|faire un vote|je veux voter|participer au vote).*', function (BotMan $bot) use ($currentUrl) {
            if ($currentUrl == '/vote/fifa') {
                $bot->reply("Vous êtes sur la page de Vote (3ème onglet).");
                $bot->reply("Voici comment exprimer votre vote :");
                $bot->reply("1. Choisissez d'abord un Thème (ex: Ballon d'Or 2025 ou Trophée Yachine).");
                $bot->reply("2. Pour chaque emplacement (Joueur 1 à 4)"); 
                $bot->reply("3. sélectionnez un nom et attribuez-lui un classement (1er, 2ème, etc.).");
            if (Auth::check()) {
                $bot->reply("4. Une fois vos 4 choix faits, cliquez sur le bouton bleu Valider pour enregistrer votre vote.");
            } else {
                $bot->reply("⚠️ Attention : Vous devez être connecté pour valider votre vote.");
                $bot->reply("👉 <a href='/se_connecter'>🔑 Connectez-vous ici</a> avant de remplir le formulaire.");
            }
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
        // --- 3. AIDE ÉTENDUE (PLUS DE CONTENU COMME DEMANDÉ) ---
        $botman->hears('.*aide.*', function (BotMan $bot) {
            $bot->reply("Bonjour ! Voici un guide complet pour vous aider :");
            $bot->reply("🛍️ ACHATS : Pour trouver un produit, dites par exemple : j'aimerais acheter un ballon. Je vous expliquerai comment le trouver et le personnaliser.");
            $bot->reply("📂 NAVIGATION : Voici l'ordre de vos onglets :");
            $bot->reply("1. Accueil, 2. Fifa Store, 3. Vote, 4. Les joueurs, 5. Les Articles, 6. Mon Panier.");
            $bot->reply("👤 COMPTE : Dites profil pour modifier vos infos ou commandes pour voir vos achats.");
            $bot->reply("💡 INFOS : La connexion est obligatoire pour ajouter au panier et choisir taille/couleur.");
            if (Auth::check()) {
                $bot->reply("Vous êtes actuellement connecté sous le nom : " . Auth::user()->name);
            }
        });

        $botman->fallback(function ($bot) {
            $bot->reply("Désolé, je n'ai pas compris. Tapez aide pour voir le guide complet.");
        });

        $botman->listen();
    }
}