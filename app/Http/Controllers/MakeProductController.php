<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Colori;
use App\Models\Taille;
use Illuminate\Support\Facades\Storage;
use App\Models\Photo;
use App\Models\Nation;
use App\Models\Categorie_Produit;
use App\Models\Variante_produit;
use App\Models\Sous_categorie_produit;
class MakeProductController extends Controller
{

public function create()
{
    $couleurs = Colori::orderBy('label_colori')->get();   
    $tailles = Taille::orderBy('label_taille')->get();     
    $nations = Nation::orderBy('nom_nation')->get();       
    $categories = Categorie_Produit::whereNull('sous_categorie')
        ->orderBy('label_categorie')->get();              


    return view('product_creation', compact('couleurs', 'tailles', 'nations',    'categories'));
}


public function store(Request $request)
{
    // Validate input
    $request->validate([
        'label_produit' => 'required|string|max:255',
        'prix_base' => 'required|numeric|min:0',
        'description_produit' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'id_nation' => 'required|exists:nation,id_nation',
        'id_categorie' => 'required|exists:categorie_produit,id_categorie',
        'couleurs' => 'required|array',
        'couleurs.*' => 'exists:colori,id_colori',
        'tailles' => 'required|array',
        'tailles.*' => 'exists:taille,id_taille',
    ], ['image.max' => "La taille de l'image ne peut pas dépasser 2048px"]);

    // Create the product
    $product = Produit::create([
        'label_produit' => $request->label_produit,
        'prix_base' => $request->prix_base,
        'description_produit' => $request->description_produit,
        'id_nation' => $request->id_nation,
        'id_categorie' => $request->id_categorie,
    ]);

    // Handle image upload or default
    $dest = public_path('assets/photo_produit');
    if (!is_dir($dest)) mkdir($dest, 0755, true);

    if ($request->hasFile('image')) {
        $newName = $product->id_produit . '.webp';
        $request->file('image')->move($dest, $newName);
        $photoPath = 'assets/photo_produit/' . $newName;
    } else {
        
        $photoPath = 'assets/photo_produit/33.webp'; 

    $product->photo()->create([
        
        'code_photo' => $photoPath
    ]);

    $color_ids = $request->input('couleurs', []);
    $size_ids  = $request->input('tailles', []);
    
    // If sent as ["1,3,4"] → explode
    if (count($color_ids) === 1 && str_contains($color_ids[0], ',')) {
        $color_ids = explode(',', $color_ids[0]);
    }
    
    if (count($size_ids) === 1 && str_contains($size_ids[0], ',')) {
        $size_ids = explode(',', $size_ids[0]);
    }
    
    // Clean + normalize
    $color_ids = array_values(array_unique(array_map('intval', $color_ids)));
    $size_ids  = array_values(array_unique(array_map('intval', $size_ids)));
    
    // Insert variants safely
    foreach ($color_ids as $color_id) {
        foreach ($size_ids as $size_id) {
            Variante_produit::firstOrCreate(
                [
                    'id_produit' => $product->id_produit,
                    'id_colori'  => $color_id,  
                    'id_taille'  => $size_id,
                ],
                ['quantitee_stock' => 0]
            );
        }
    }

    return redirect()->route('product.index')->with('success', 'Produit créé avec succès !');
}


 

}
}