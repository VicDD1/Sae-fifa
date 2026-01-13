<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\GroqService;
use App\Services\ProductSearchService;

class BotManController extends Controller
{
    protected GroqService $groqService;
    protected ProductSearchService $productSearchService;

    public function __construct(GroqService $groqService, ProductSearchService $productSearchService)
    {
        $this->groqService = $groqService;
        $this->productSearchService = $productSearchService;
    }

    public function handle(Request $request)
    {
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
        $botman = BotManFactory::create([]);    
        
        $combinedId = $request->input('userId');
        $currentUrl = str_contains($combinedId, '|') ? explode('|', $combinedId)[1] : '/';
        $currentUrl = '/' . ltrim($currentUrl, '/');    

        $groqService = $this->groqService;
        $productSearchService = $this->productSearchService;

        // --- DEBUG ---
        $botman->hears('debug url', function (BotMan $bot) use ($currentUrl) {
            $bot->reply("URL extraite avec succès : " . $currentUrl);
        });

        // --- MENU AIDE RAPIDE (réponse locale pour rapidité) ---
        $botman->hears('aide', function (BotMan $bot) {
            $this->showHelpMenu($bot);
        });

        // --- COMMANDES DE RECHERCHE DE PRODUITS ---
        
        // Rechercher un produit par mot-clé
        $botman->hears('chercher {keyword}', function (BotMan $bot, $keyword) use ($productSearchService) {
            $products = $productSearchService->searchProducts($keyword);
            $response = $productSearchService->formatSearchResultsForChat($products);
            $bot->reply($response);
        });

        $botman->hears('rechercher {keyword}', function (BotMan $bot, $keyword) use ($productSearchService) {
            $products = $productSearchService->searchProducts($keyword);
            $response = $productSearchService->formatSearchResultsForChat($products);
            $bot->reply($response);
        });

        // Vérifier le stock d'un produit
        $botman->hears('stock {productName}', function (BotMan $bot, $productName) use ($productSearchService) {
            $stockInfo = $productSearchService->checkProductStock($productName);
            $response = $productSearchService->formatStockInfoForChat($stockInfo);
            $bot->reply($response);
        });

        // Voir les produits en stock
        $botman->hears('produits en stock', function (BotMan $bot) use ($productSearchService) {
            $products = $productSearchService->getProductsInStock(10);
            $response = $productSearchService->formatSearchResultsForChat($products);
            $bot->reply("📦 Voici les produits actuellement en stock :\n\n" . $response);
        });

        // Voir les catégories
        $botman->hears('catégories', function (BotMan $bot) use ($productSearchService) {
            $response = $productSearchService->formatCategoriesForChat();
            $bot->reply($response);
        });

        $botman->hears('categories', function (BotMan $bot) use ($productSearchService) {
            $response = $productSearchService->formatCategoriesForChat();
            $bot->reply($response);
        });

        // Rechercher par catégorie
        $botman->hears('catégorie {categoryName}', function (BotMan $bot, $categoryName) use ($productSearchService) {
            $products = $productSearchService->searchByCategory($categoryName);
            $response = $productSearchService->formatSearchResultsForChat($products);
            $bot->reply($response);
        });

        $botman->hears('categorie {categoryName}', function (BotMan $bot, $categoryName) use ($productSearchService) {
            $products = $productSearchService->searchByCategory($categoryName);
            $response = $productSearchService->formatSearchResultsForChat($products);
            $bot->reply($response);
        });

        // --- TOUTES LES AUTRES REQUÊTES PASSENT PAR L'IA ---
        $botman->fallback(function ($bot) use ($groqService, $productSearchService, $currentUrl) {
            $userMessage = $bot->getMessage()->getText();
            
            // Détecter les intentions de recherche de produit et stock
            $dbResponse = $this->handleDatabaseQuery($userMessage, $productSearchService);
            
            if ($dbResponse !== null) {
                $bot->reply($dbResponse);
                return;
            }
            
            // Enrichir le message avec le contexte utilisateur
            $contextInfo = $this->buildUserContext();
            $enrichedMessage = $userMessage . "\n\n[CONTEXTE UTILISATEUR: " . $contextInfo . "]";
            
            $aiResponse = $groqService->chat($enrichedMessage, $currentUrl);
            $bot->reply($aiResponse);
        });

        $botman->listen();
    }

    /**
     * Gère les requêtes liées à la base de données (produits, stock)
     */
    protected function handleDatabaseQuery(string $message, ProductSearchService $productSearchService): ?string
    {
        $messageLower = mb_strtolower($message);
        
        // Détection de recherche de stock
        if (preg_match('/(stock|disponible|disponibilité|dispo|reste).*(de|du|des|pour)?\s*(.+)/i', $message, $matches)) {
            $productName = trim($matches[3]);
            if (strlen($productName) > 2) {
                $stockInfo = $productSearchService->checkProductStock($productName);
                return $productSearchService->formatStockInfoForChat($stockInfo);
            }
        }
        
        // Détection de recherche de produit
        if (preg_match('/(cherche|recherche|trouve|trouver|acheter|achat|je veux|j\'aimerais|montrer?)\s+(un|une|des|le|la|les)?\s*(.+)/i', $message, $matches)) {
            $keyword = trim($matches[3]);
            // Nettoyer les mots inutiles à la fin
            $keyword = preg_replace('/(s\'il te plaît|s\'il vous plaît|svp|stp|merci)$/i', '', $keyword);
            $keyword = trim($keyword);
            
            if (strlen($keyword) > 2) {
                $products = $productSearchService->searchProducts($keyword);
                if ($products->isNotEmpty()) {
                    return $productSearchService->formatSearchResultsForChat($products);
                }
            }
        }
        
        // Détection de demande de catégories
        if (preg_match('/(catégorie|categorie|type|genre).*(produit|article)?/i', $message)) {
            if (preg_match('/(catégorie|categorie)\s+(.+)/i', $message, $matches)) {
                $categoryName = trim($matches[2]);
                $products = $productSearchService->searchByCategory($categoryName);
                return $productSearchService->formatSearchResultsForChat($products);
            }
            return $productSearchService->formatCategoriesForChat();
        }
        
        // Détection de demande de produits en stock
        if (preg_match('/(quoi|qu\'est-ce|produit?|articles?).*(en stock|disponible)/i', $message) ||
            preg_match('/(en stock|disponible).*(quoi|produit?|articles?)/i', $message)) {
            $products = $productSearchService->getProductsInStock(10);
            return "📦 Voici les produit actuellement en stock :\n\n" . $productSearchService->formatSearchResultsForChat($products);
        }
        
        return $productSearchService->getProductsInStock(10); 
    }

    /**
     * Construit le contexte utilisateur pour enrichir les requêtes IA
     */
    protected function buildUserContext(): string
    {
        if (Auth::check()) {
            $user = Auth::user();
            $context = "Connecté en tant que " . ($user->name ?? 'Utilisateur');
            if ($user->professionnel ?? false) {
                $context .= " (compte professionnel)";
            }
            return $context;
        }
        return "Non connecté";
    }

    /**
     * Affiche le menu d'aide complet
     */
    protected function showHelpMenu(BotMan $bot): void
    {
        $bot->reply("Bonjour ! Voici votre guide complet pour utiliser toutes les fonctionnalités du site :");
        $bot->reply("📂 NAVIGATION (Vos onglets dans l'ordre) :");
        $bot->reply("1. Accueil | 2. Fifa Store | 3. Vote | 4. Les joueurs | 5. Les Articles | 6. L'Actu | 7. Mon Panier.");
        $bot->reply("- Pour revenir au début du site, vous pouvez dire : accueil.");
        $bot->reply("🔍 RECHERCHE DE PRODUITS :");
        $bot->reply("- Chercher un produit : dites 'chercher maillot' ou 'je veux acheter un ballon'");
        $bot->reply("- Voir le stock : dites 'stock maillot' ou 'est-ce que le ballon est disponible ?'");
        $bot->reply("- Voir les catégories : dites 'catégories'");
        $bot->reply("- Produits en stock : dites 'produits en stock' ou 'qu'est-ce qui est disponible ?'");
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
    }
}