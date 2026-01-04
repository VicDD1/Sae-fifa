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
        
        $combinedId = $request->input('userId');
        $currentUrl = str_contains($combinedId, '|') ? explode('|', $combinedId)[1] : '/';
        $currentUrl = '/' . ltrim($currentUrl, '/');    

        // --- CONFIGURATION ET DEBUG ---

        $botman->hears('debug url', function (BotMan $bot) use ($currentUrl) {
            $bot->reply("URL extraite avec succès : " . $currentUrl);
        });

        // --- MOTEUR DE RECHERCHE PRODUITS ---

        $botman->hears('.*(acheter|chercher|trouver|vouloir|besoin|aimerais) (.*)', function (BotMan $bot, $action, $phraseSaisie) use ($currentUrl){
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

                $bot->reply("Pour trouver " . $produitNettoye . " sur notre boutique, voici la marche à suivre :");
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

        // --- NAVIGATION PRINCIPALE ---

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

        // --- TUNNEL DE COMMANDE ET PAIEMENT ---

        $botman->hears('.*(commande|achat|mes commandes).*', function (BotMan $bot) use ($currentUrl) {
            if (Auth::check()) {
                if ($currentUrl == '/commande') {
                    $bot->reply("Vous êtes sur la première étape de votre commande.");
                    $bot->reply("- Astuce : Une fois l'adresse remplie, la ville et le code postal se complètent automatiquement.");
                    $bot->reply("- Validation : Vérifiez vos infos de livraison puis cliquez sur le bouton bleu Valider la commande ou ici : <br><a href='/confirmation_commande'>💳 Valider ma commande</a>.");
                }
                elseif ($currentUrl == '/confirmation_commande') {
                    $bot->reply("Vous êtes à l'étape du paiement sécurisé.");
                    $bot->reply("- Titulaire : Saisissez le nom présent sur votre carte bancaire.");
                    $bot->reply("- Carte : Entrez vos numéros de carte, la date d'expiration (MM/AA) et le code CVV à 3 chiffres au dos.");
                    $bot->reply("- Finalisation : Cliquez sur le bouton bleu Confirmer et payer pour valider définitivement votre achat.");
                }
                elseif ($currentUrl == '/mes_commandes') {
                    $bot->reply("Vous visualisez l'historique de vos achats.");
                    $bot->reply("- Voir les détails : Cliquez sur n'importe quelle carte de commande (ex: Commande #123) pour voir la liste des articles achetés.");
                    $bot->reply("- Icônes : Le symbole + devient - une fois la commande dépliée.");
                    $bot->reply("- Infos disponibles : Pour chaque achat, vous retrouvez le montant total, le mode de paiement et le statut (Payé ou en attente).");
                }
                else {
                    $bot->reply("Pour voir vos commande, veuillez soit cliquer sur le bouton bleu Mes commandes à droite de votre nom ou cliquer sur ce lien :<br><a href='/mes_commandes'>📦 Mes commandes</a>");
                }
            } else {
                $bot->reply("Vous devez être connecté pour voir vos commandes. Cliquez sur l'icône de profil à droite.");
            }
        });

        // --- GESTION DU PROFIL ET PARAMETRES ---

        $botman->hears('.*(profil|compte|modifier|paramètre|voir mon compte).*', function (BotMan $bot)use ($currentUrl) {
            if (Auth::check()) {
                if($currentUrl=='/mon-profil'){
                    $bot->reply("Vous visualisez actuellement vos informations personnelles.");
                    $bot->reply("Pour pouvoir faire des changements, cliquez sur le bouton bleu MODIFIER MES INFOS situé tout en bas de la page.");
                    $bot->reply("Vous pouvez aussi utiliser ce lien direct : <a href='/parametre_compte'>👤 Modifier mon compte</a>");
                }
                elseif($currentUrl == "/parametre_compte"){
                    $bot->reply("Vous êtes sur la page de modification de vos informations.");
                    $bot->reply("- Champs modifiables : Vous pouvez changer votre Prénom, Surnom, Email, Date de naissance et Equipe favorite.");
                    $bot->reply("- Sécurité : Pour protéger votre compte, changez votre mot de passe ou activez la Double Authentification par SMS via le bouton bleu ACTIVER.");
                    $bot->reply("Important : Une fois vos changements faits, cliquez sur le bouton vert ENREGISTRER LES MODIFICATIONS en bas de page pour valider.");
                }
                else{
                    $bot->reply("Pour avoir ou modifier vos informations, veuillez soit cliquer sur votre nom ( " . Auth::user()->name . " ) dans la barre bleue ou cliquer sur ce lien :<br><a href='/mon-profil'>👤 Afficher mon compte</a>");
                }
            } else {
                $bot->reply("Veuillez vous connecter pour accéder à votre profil.");
            }
        });

        // --- ESPACE PROFESSIONNEL (DEMANDES ET INSCRIPTION PRO) ---

        $botman->hears('.*(professionnel|compte pro|devenir partenaire|proposer|demande).*', function (BotMan $bot) use ($currentUrl) {
            if (Auth::check()) {
                if (Auth::user()->professionnel) {
                    if ($currentUrl == '/proposer_un_produit') {
                        $bot->reply("Vous êtes sur le formulaire de proposition de produit.");
                        $bot->reply("- Produit : Saisissez le nom complet de l'article dans le premier champ.");
                        $bot->reply("- Description : Donnez un maximum de détails dans le second champ.");
                        $bot->reply("- Envoi : Cliquez sur le bouton bleu CRÉER LA PROPOSITION.");
                    } else {
                        $bot->reply("En tant que professionnel, vous pouvez proposer de nouveaux articles.");
                        $bot->reply("Cliquez sur l'onglet faire une demande de produit à droite ou ici : <br><a href='/proposer_un_produit'>💡 Faire une demande</a>");
                    }
                } 
                else {
                    if ($currentUrl == '/') {
                        $bot->reply("Bonjour " . Auth::user()->name . " ! Pour devenir professionnel, cliquez sur le bouton Compte professionnel en haut à droite.");
                        $bot->reply("Ou utilisez ce lien direct : <br><a href='/creer_un_compte_professionnel_1'>💼 Devenir Professionnel</a>");
                    }
                    if ($currentUrl == '/creer_un_compte_professionnel_1') {
                        $bot->reply("Etape 1/2 : Informations entreprise.");
                        $bot->reply("- Saisie : Remplissez le nom de la société, le numéro de TVA, l'activité et vos coordonnées complètes.");
                        $bot->reply("- Suite : Cliquez sur le bouton bleu POURSUIVRE.");
                    }
                    if ($currentUrl == '/creer_un_compte_professionnel_2') {
                        $bot->reply("Etape 2/2 : Sécurisation.");
                        $bot->reply("- Mot de passe : Saisissez et confirmez votre mot de passe.");
                        $bot->reply("- Légalité : Cochez la case des conditions d'utilisation puis cliquez sur CRÉER LE COMPTE.");
                    }
                }
            } else {
                $bot->reply("Désolé, cette fonctionnalité nécessite d'être connecté.");
                $bot->reply("Si vous voulez devenir professionnel, connectez-vous d'abord à votre compte client : <br><a href='/se_connecter'>🔑 Se connecter</a>");
            }
        });

        // --- SYSTEME DE VOTE ---

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

        // --- LISTE DES JOUEURS ---

        $botman->hears('.*(joueur|les joueurs|players).*', function (BotMan $bot) use ($currentUrl) {
            if ($currentUrl == '/players') {
                $bot->reply("Vous consultez la liste Les joueurs (4ème onglet).");
            } else {
                $bot->reply("Pour voir l'effectif, veuillez soit cliquer sur l'onglet Les joueurs en 4ème position ou cliquer sur ce lien :<br><a href='/players'>🏃 Voir les joueurs</a>");
            }
        });

        // --- ARTICLES ET ACTUALITES ---

        $botman->hears('.*(article|actualité|news).*', function (BotMan $bot) {
            $bot->reply("L'onglet Les Articles (5ème position) est en cours de préparation.");
        });

        // --- PANIER ET MODIFICATIONS ---

        $botman->hears('.*(panier|mon panier).*', function (BotMan $bot) use($currentUrl) {
            if ($currentUrl == '/panier') {
                $bot->reply("Vous êtes actuellement dans votre panier.");
                $bot->reply("- Quantité : Utilisez les boutons + ou - à côté de l'article pour changer le nombre.");
                $bot->reply("- Suppression : Cliquez sur le lien rouge Supprimer sous l'image pour retirer un produit.");
                $bot->reply("- Etape suivante : Pour payer, cliquez sur le bouton jaune PASSER LA COMMANDE ou sur ce lien : <br><a href='/commande'>💳 Passer a la commande</a>.");
            }
            else{
                $bot->reply("Pour accéder au panier, veuillez soit cliquer sur l'onglet Mon Panier en 6ème position ou cliquer sur ce lien :<br><a href='/panier'>🛒 Voir mon Panier</a>");
            }
        });

        // --- CONNEXION ET DECONNEXION ---

        $botman->hears('.*(connexion|connecter|login|créer un compte|inscription).*', function (BotMan $bot) use ($currentUrl) {
            if (Auth::check()) {
                $bot->reply("Bonjour " . Auth::user()->name . " ! Vous êtes déjà connecté.");
                $bot->reply("Pour quitter votre session, cliquez sur l'icône Power rouge à droite ou ici : <br><a href='/logout'>🚪 Se déconnecter</a>");
            } else {
                if ($currentUrl == '/se_connecter') {
                    $bot->reply("Vous êtes sur la page de connexion.");
                    $bot->reply("- Déjà inscrit : Saisissez votre email et votre mot de passe pour accéder à votre compte.");
                    $bot->reply("- Une fois que vous avez entrez vos informations, vous pouvez maintenant cliquer sur le bouton bleu se connecter qui se trouve juste en dessous du mot de passe");
                    $bot->reply("- Nouveau client : Cliquez sur le premier bouton pour créer votre compte standard : <br><a href='/creer_un_compte_1'>👤 Créer un compte particulier</a>");
                    $bot->reply("- Professionnel : Si vous êtes un partenaire, utilisez le deuxième bouton : <br><a href='/creer_un_compte_professionnel_1'>💼 Créer un compte pro</a>");
                } 
                else {
                    $bot->reply("Pour accéder à votre espace ou créer un compte, cliquez sur l'icône de profil grise à droite ou ici : <br><a href='/se_connecter'>🔑 Se connecter / S'inscrire</a>");
                }
            }
        });

        // --- INSCRIPTION CLIENT (ETAPES 1 ET 2) ---

        $botman->hears('.*(créer un compte|création de compte|inscrire|inscription).*', function (BotMan $bot) use ($currentUrl) {
            if (str_contains(request()->fullUrl(), 'professionnel')) {
                $bot->reply("Pour créer un compte professionnel, vous devez impérativement posséder un compte client standard au préalable.");
                $bot->reply("Cette démarche s'effectue uniquement depuis la page d'accueil une fois connecté.");
            }
            if ($currentUrl == '/creer_un_compte_1') {
                $bot->reply("Vous êtes à l'étape 1 sur 2 de votre inscription.");
                $bot->reply("- Informations : Remplissez votre prénom, adresse électronique et pseudonyme.");
                $bot->reply("- Profil : Indiquez votre date de naissance, pays, équipe favorite et langue.");
                $bot->reply("- Suite : Cliquez sur le bouton bleu POURSUIVRE pour passer à l'étape suivante.");
            }
            if ($currentUrl == '/creer_un_compte_2') {
                $bot->reply("Vous êtes à l'étape 2 sur 2 : Sécurisation du compte.");
                $bot->reply("- Mot de passe : Choisissez un mot de passe et confirmez-le dans le second champ.");
                $bot->reply("- Légalité : Vous devez impérativement cocher la case d'acceptation des conditions d'utilisation.");
                $bot->reply("- Finalisation : Cliquez sur le bouton bleu CRÉER LE COMPTE pour valider votre inscription.");
            }
            if ($currentUrl != '/creer_un_compte_1' && $currentUrl != '/creer_un_compte_2') {
                $bot->reply("Pour commencer votre inscription, veuillez vous rendre sur la page de création de compte :");
                $bot->reply("👉 <a href='/creer_un_compte_1'>👤 Créer mon compte client</a>");
            }
        });

        // --- MENU AIDE ET FALLBACK ---

        $botman->hears('.*aide.*', function (BotMan $bot) {
            $bot->reply("Bonjour ! Voici votre guide complet pour utiliser toutes les fonctionnalités du site :");
            $bot->reply("📂 NAVIGATION (Vos onglets dans l'ordre) :");
            $bot->reply("1. Accueil | 2. Fifa Store | 3. Vote | 4. Les joueurs | 5. Les Articles | 6. Mon Panier.");
            $bot->reply("- Pour revenir au début du site, vous pouvez dire : accueil.");
            $bot->reply("🛍️ BOUTIQUE ET ACHATS :");
            $bot->reply("- Recherche : Pour trouver un article, dites : j'aimerais acheter un ballon (remplacez ballon par l'article voulu).");
            $bot->reply("- Panier : Gérez vos quantités ou supprimez des produits sur la page /panier.");
            $bot->reply("- Paiement : Pour commander, cliquez sur le bouton jaune PASSER LA COMMANDE dans le panier.");
            $bot->reply("⚽ PARTICIPER AUX VOTES :");
            $bot->reply("- Accès : Rendez-vous sur le 3ème onglet ou cliquez ici : <a href='/vote/fifa'>⚽ Voter ici</a>.");
            $bot->reply("- Méthode : Choisissez un thème puis sélectionnez vos 4 joueurs avec leur rang (1er, 2ème, etc.).");
            $bot->reply("- Envoi : Cliquez sur le bouton bleu Valider pour enregistrer votre participation.");
            $bot->reply("👤 GESTION DU COMPTE :");
            $bot->reply("- Profil : Dites profil pour modifier vos infos personnelles ou activer la sécurité par SMS.");
            $bot->reply("- Commandes : Dites commandes pour voir vos anciens achats ou compléter une commande en cours.");
            $bot->reply("- Inscription : Dites connexion pour créer un compte client ou professionnel.");
            $bot->reply("💼 PARTENAIRES PROFESSIONNELS :");
            if (Auth::check() && Auth::user()->professionnel) {
                $bot->reply("- Demandes : Utilisez le formulaire Proposer un produit pour nous soumettre vos articles.");
            } else {
                $bot->reply("- Devenir Pro : Si vous êtes déjà client, dites : compte pro pour découvrir comment devenir partenaire.");
            }
            if (Auth::check()) {
                $bot->reply("Statut : Vous êtes actuellement connecté en tant que " . Auth::user()->name . ".");
            } else {
                $bot->reply("💡 Conseil : Connectez-vous pour pouvoir ajouter des produits au panier et voter.");
            }
        });

        $botman->fallback(function ($bot) {
            $bot->reply("Désolé, je n'ai pas compris. Tapez aide pour voir le guide complet.");
        });

        $botman->listen();
    }
}