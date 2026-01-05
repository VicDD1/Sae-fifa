<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Nation;
use App\Models\Taille;
use App\Models\Colori;
use App\Models\Photo;
use App\Models\Stock;
use App\Models\Categorie_Produit;
use App\Models\Variante_produit;
class ProductController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. CHARGEMENT DES DONNÉES POUR LES LISTES DÉROULANTES ---

        // NATIONS (inchangé)
        $nations = Nation::whereIn('id_nation', function ($query) {
            $query->select('id_nation')->from('produit')->whereNotNull('id_nation');
        })->orderBy('nom_nation')->get();

        // CATEGORIES (Parents uniquement : ceux qui n'ont pas de parent défini dans 'sous_categorie')
        $categories = Categorie_Produit::whereNull('sous_categorie')
            ->orderBy('label_categorie')
            ->get();

        // TAILLES (inchangé)
        $tailles = Taille::whereIn('id_taille', function ($query) {
            $query->select('id_taille')->from('variante_produit')->whereNotNull('id_taille');
        })->orderBy('label_taille')->get();

        // COULEURS (inchangé)
        $couleurs = Colori::orderBy('id_colori')->get();
        


        $idProduit = $request->id_produit;
        $idTaille  = $request->id_taille;
        $idColori  = $request->id_colori;
        
        // On ne charge les sous-catégories que si une catégorie parent est sélectionnée
        $sous_categories = collect(); // On démarre avec une collection vide

        if ($request->filled('id_categorie')) {
            // C'est ici que l'on applique votre logique SQL :
            $sous_categories = Categorie_Produit::where('sous_categorie', $request->id_categorie)
                ->orderBy('label_categorie')
                ->get();
        }


        // --- 2. REQUÊTE PRODUITS ---
        
        $query = Produit::query();
        $query->select('produit.*')->distinct();

        // RECHERCHE TEXTUELLE
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('label_produit', 'ILIKE', $searchTerm)
                ->orWhere('description_produit', 'ILIKE', $searchTerm);
            });
        }

        if ($request->filled('id_nation')) {
            $query->where('produit.id_nation', $request->id_nation);
        }

        if ($request->filled('id_categorie')) {
            
            if (!$request->filled('sous_categorie')) {
                $idsEnfants = Categorie_Produit::where('sous_categorie', $request->id_categorie)
                    ->pluck('id_categorie') // On ne prend que la colonne ID
                    ->toArray();
                $tousLesIds = array_merge([$request->id_categorie], $idsEnfants);
                $query->whereIn('produit.id_categorie', $tousLesIds);
            }
        }

        // FILTRE SOUS-CATEGORIE (Enfant)
        if ($request->filled('sous_categorie')) {
            $query->where('produit.id_categorie', $request->sous_categorie);
        }

        // FILTRE PRIX
        if ($request->filled('max_price')) {
            $query->where('produit.prix_base', '<=', $request->max_price);
        }

        // FILTRE TAILLE
        if ($request->filled('id_taille')) {
            $query->join('variante_produit as vp_taille', 'produit.id_produit', '=', 'vp_taille.id_produit')
                ->where('vp_taille.id_taille', $request->id_taille);
        }

        // FILTRE COULEUR
        if ($request->filled('id_colori')) {
            $query->join('variante_produit as vp_colori', 'produit.id_produit', '=', 'vp_colori.id_produit')
                ->where('vp_colori.id_colori', $request->id_colori);
        }

        // TRI
        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('produit.prix_base', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('produit.prix_base', 'desc');
            }
        }

        $products = $query->get();


        $historyIds = session()->get('recent_products', []);
        $recentProducts = collect();
            
        if (!empty($historyIds)) {
            // On construit la chaîne CASE WHEN pour PostgreSQL
            $orderByCase = 'CASE ';
            foreach ($historyIds as $index => $id) {
                $orderByCase .= "WHEN id_produit = " . (int)$id . " THEN " . $index . " ";
            }
            $orderByCase .= 'END';
        
            $recentProducts = Produit::whereIn('id_produit', $historyIds)
                ->orderByRaw($orderByCase)
                ->get();
        }


        return view('products', compact('products', 'nations', 'tailles', 'couleurs', 'categories', 'sous_categories','recentProducts'));
    }
    public function detail(Request $request, $id)
    {
        $history = session()->get('recent_products', []);
        if (($key = array_search($id, $history)) !== false) unset($history[$key]);
            array_unshift($history, $id);
        session()->put('recent_products', array_slice($history, 0, 10));
        // Charger le produit demandé
        $product = Produit::with(['couleurs', 'tailles'])->findOrFail($id);
        //stocks
        $stock = null;

        $idTaille = $request->id_taille ?? ($product->tailles->first()->id_taille ?? null);
        $idColori = $request->id_colori ?? ($product->couleurs->first()->id_colori ?? null);


        if ($idTaille && $idColori) {
            $stock = Variante_produit::where('id_produit', $product->id_produit)
                ->where('id_taille', $idTaille)
                ->where('id_colori', $idColori)
                ->value('quantitee_stock');
            }       

        // Trouver des produits similaires :
        // même catégorie ou même sous-catégorie, mais exclure le produit lui-même
        $similarProducts = Produit::where(function ($q) use ($product) {
                $q->where('id_categorie', $product->id_categorie);
            })
            ->where('id_produit', '!=', $product->id_produit)
            ->limit(10)
            ->get();

        return view('product_detail', compact('product', 'similarProducts','stock'));
    }
    public function getStock(Request $request)
    {
        $idProduit = $request->id_produit;
        $idTaille  = $request->id_taille;
        $idColori  = $request->id_colori;
    
        // Vérifier que toutes les valeurs sont présentes
        if(!$idProduit || !$idTaille || !$idColori) {
            return response()->json(['stock' => null]);
        }
    
        $stock = Variante_produit::where('id_produit', $idProduit)
            ->where('id_taille', $idTaille)
            ->where('id_colori', $idColori)
            ->value('quantitee_stock');
    
        return response()->json([
            'stock' => $stock ?? 0
        ]);
    }
    public function create()
    {
        $categories = Categorie_Produit::all();
        $nations    = Nation::all();
        $tailles    = Taille::all();
        $coloris    = Colori::all();

        return view('products_create', compact('categories', 'nations', 'tailles', 'coloris'));
    }

    // Traite le formulaire
public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'nom_produit'         => 'required|string|max:50',
            'prix_base'           => 'required|numeric',
            'description_produit' => 'nullable|string|max:500',
            'id_categorie'        => 'required|exists:categorie_produit,id_categorie',
            'id_nation'           => 'required|exists:nation,id_nation',
            'quantite'            => 'required|integer|min:0',
            'id_taille'           => 'required|exists:taille,id_taille',
            'id_colori'           => 'required|exists:colori,id_colori',
            'photo'               => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Création du Produit
        // ATTENTION : On utilise "new Produit()" (le nom de ta classe)
        $product = new Produit(); 
        
        // MAPPING : Formulaire (nom_produit) -> Base de données (label_produit)
        $product->label_produit       = $request->nom_produit; 
        
        $product->prix_base           = $request->prix_base;
        $product->description_produit = $request->description_produit;
        $product->id_categorie        = $request->id_categorie;
        $product->id_nation           = $request->id_nation;
        
        $product->save(); 

        // 3. Gestion de la Photo
        if ($request->hasFile('photo')) {
        // A. On récupère le fichier
        $file = $request->file('photo');
        
        // B. On génère un nom unique pour éviter d'écraser une autre image
        // Ex: 17043829_monimage.jpg
        $filename = time() . '_' . $file->getClientOriginalName();
        
        // C. On déplace le fichier dans public/assets/photo_produit
        $file->move(public_path('assets/photo_produit'), $filename);

        // D. Enregistrement en base de données
        $photo = new Photo();
        
        // On enregistre le chemin relatif ou le nom du fichier
        // Ici je mets le chemin complet pour que ce soit facile à afficher plus tard
        $photo->code_photo = 'assets/photo_produit/' . $filename; 
        
        $photo->id_produit = $product->id_produit;
        $photo->save();
    }

        // 4. Création du Stock
    $stock = new Stock();
    $stock->id_produit = $product->id_produit;
    $stock->id_taille  = $request->id_taille;
    $stock->id_colori  = $request->id_colori;
    
    // CORRECTION ICI (Double 'e')
    // A gauche : nom de la colonne dans la BDD (quantitee_stock)
    // A droite : nom du champ dans le formulaire HTML (quantite)
    $stock->quantitee_stock = $request->quantite; 
    
    $stock->save();

    return redirect()->route('products.index')->with('success', 'Produit créé avec succès !');
}



}