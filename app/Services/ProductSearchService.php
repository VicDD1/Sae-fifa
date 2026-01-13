<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\Variante_produit;
use App\Models\Categorie_Produit;
use Illuminate\Support\Collection;

class ProductSearchService
{
    /**
     * Recherche des produits par mot-clé (nom, description, catégorie)
     */
    public function searchProducts(string $keyword, int $limit = 5): Collection
    {
        $keyword = strtolower(trim($keyword));
        
        return Produit::with(['photo', 'variantes.taille', 'variantes.colori'])
            ->where(function($query) use ($keyword) {
                $query->whereRaw('LOWER(label_produit) LIKE ?', ["%{$keyword}%"])
                      ->orWhereRaw('LOWER(description_produit) LIKE ?', ["%{$keyword}%"]);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Recherche des produits par catégorie
     */
    public function searchByCategory(string $categoryName, int $limit = 5): Collection
    {
        $category = Categorie_Produit::whereRaw('LOWER(label_categorie) LIKE ?', ['%' . strtolower($categoryName) . '%'])
            ->first();

        if (!$category) {
            return collect();
        }

        return Produit::with(['photo', 'variantes.taille', 'variantes.colori'])
            ->where('id_categorie', $category->id_categorie)
            ->limit($limit)
            ->get();
    }

    /**
     * Vérifie le stock d'un produit spécifique
     */
    public function checkProductStock(string $productName): array
    {
        $produit = Produit::with(['variantes.taille', 'variantes.colori'])
            ->whereRaw('LOWER(label_produit) LIKE ?', ['%' . strtolower($productName) . '%'])
            ->first();

        if (!$produit) {
            return ['found' => false, 'message' => "Produit non trouvé"];
        }

        $stockInfo = [];
        $totalStock = 0;

        foreach ($produit->variantes as $variante) {
            $quantite = $variante->quantite_stock ?? $variante->quantitee_stock ?? 0;
            $totalStock += $quantite;
            
            $taille = $variante->taille->label_taille ?? 'Taille unique';
            $couleur = $variante->colori->label_colori ?? 'Standard';
            
            if ($quantite > 0) {
                $stockInfo[] = [
                    'taille' => $taille,
                    'couleur' => $couleur,
                    'quantite' => $quantite
                ];
            }
        }

        return [
            'found' => true,
            'produit' => $produit->label_produit,
            'prix' => $produit->prix_base,
            'total_stock' => $totalStock,
            'disponible' => $totalStock > 0,
            'variantes_disponibles' => $stockInfo
        ];
    }

    /**
     * Récupère les produits en stock (avec au moins 1 unité disponible)
     */
    public function getProductsInStock(int $limit = 10): Collection
    {
        return Produit::with(['photo', 'variantes'])
            ->whereHas('variantes', function($query) {
                $query->where('quantitee_stock', '>', 0)
                      ->orWhere('quantitee_stock', '>', 0);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Récupère les produits en rupture de stock
     */
    public function getOutOfStockProducts(int $limit = 10): Collection
    {
        return Produit::with(['photo'])
            ->whereDoesntHave('variantes', function($query) {
                $query->where('variante_produit.quantitee_stock', '>', 0)
                      ->orWhere('variante_produit.quantitee_stock', '>', 0);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Formate les résultats de recherche pour le chatbot
     */
    public function formatSearchResultsForChat(Collection $products): string
    {
        if ($products->isEmpty()) {
            return "Aucun produit trouvé correspondant à votre recherche.";
        }

        $result = "🔍 J'ai trouvé " . $products->count() . " produit(s) :\n\n";
        
        foreach ($products as $produit) {
            $prix = number_format($produit->prix_base, 2, ',', ' ');
            $result .= "⚽ **{$produit->label_produit}** - {$prix}€\n";
            
            // Vérifier le stock
            $stockTotal = $produit->variantes->sum(function($v) {
                return $v->quantitee_stock ?? $v->quantitee_stock ?? 0;
            });
            
            $stockStatus = $stockTotal > 0 ? "✅ En stock ({$stockTotal})" : "❌ Rupture de stock";
            $result .= "   {$stockStatus}\n";
            $result .= "   <a href='/produit/{$produit->id_produit}'>Voir le produit</a>\n\n";
        }

        return $result;
    }

    /**
     * Formate les informations de stock pour le chatbot
     */
    public function formatStockInfoForChat(array $stockInfo): string
    {
        if (!$stockInfo['found']) {
            return "❌ " . $stockInfo['message'];
        }

        $result = "📦 **{$stockInfo['produit']}**\n";
        $result .= "💰 Prix : " . number_format($stockInfo['prix'], 2, ',', ' ') . "€\n";
        
        if ($stockInfo['disponible']) {
            $result .= "✅ En stock (Total : {$stockInfo['total_stock']} unités)\n\n";
            
            if (!empty($stockInfo['variantes_disponibles'])) {
                $result .= "Disponibilités :\n";
                foreach ($stockInfo['variantes_disponibles'] as $variante) {
                    $result .= "  • {$variante['taille']} / {$variante['couleur']} : {$variante['quantite']} unités\n";
                }
            }
        } else {
            $result .= "❌ Rupture de stock\n";
        }

        return $result;
    }

    /**
     * Récupère toutes les catégories disponibles
     */
    public function getAllCategories(): Collection
    {
        return Categorie_Produit::all();
    }

    /**
     * Formate la liste des catégories pour le chatbot
     */
    public function formatCategoriesForChat(): string
    {
        $categories = $this->getAllCategories();
        
        if ($categories->isEmpty()) {
            return "Aucune catégorie disponible pour le moment.";
        }

        $result = "📂 Catégories disponibles :\n\n";
        foreach ($categories as $categorie) {
            $result .= "• {$categorie->label_categorie}\n";
        }
        
        return $result;
    }
}
