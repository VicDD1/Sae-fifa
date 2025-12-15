<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit as Product; // Assure-toi que ton modèle s'appelle Product
use App\Models\Color;
use App\Models\Size;
use Illuminate\Support\Facades\Storage;

class MakeProductController extends Controller
{
    // Affiche le formulaire de création
    public function create()
    {
        return view('product_creation'); // nom de la vue que tu as créée
    }

    // Traite le formulaire de création
    public function store(Request $request)
    {
        $request->validate([
            'label_produit' => 'required|string|max:255',
            'prix_base' => 'required|numeric|min:0',
            'description_produit' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'couleurs' => 'nullable|array',
            'couleurs.*' => 'nullable|string|max:50',
            'tailles' => 'nullable|array',
            'tailles.*' => 'nullable|string|max:50',
        ]);
    
        // 1) créer le produit d'abord (image null temporairement)
        $product = Product::create([
            'label_produit' => $request->label_produit,
            'prix_base' => $request->prix_base,
            'description_produit' => $request->description_produit,
            'image' => null,
        ]);
    
        // 2) gérer le fichier image — on le nomme {id}.webp et on le place dans public/assets/photo_produit
        if ($request->hasFile('image')) {
            // s'assurer que le dossier existe
            $dest = public_path('assets/photo_produit');
            if (! is_dir($dest)) {
                mkdir($dest, 0755, true);
            }
    
            $newName = $product->id_produit . '.webp';
    
            // déplacer le fichier uploadé
            $request->file('image')->move($dest, $newName);
    
            // sauvegarder le chemin relatif en BDD (optionnel)
            $product->image = 'assets/photo_produit/' . $newName;
            $product->save();
        }
    
        // 3) couleurs — supprimer les valeurs vides avant création
        if ($request->filled('couleurs')) {
            $colors = array_filter($request->input('couleurs', []), fn($c) => ! is_null($c) && trim($c) !== '');
            foreach ($colors as $color) {
                $product->couleurs()->create(['label_colori' => $color]);
            }
        }
    
        // 4) tailles — idem
        if ($request->filled('tailles')) {
            $sizes = array_filter($request->input('tailles', []), fn($s) => ! is_null($s) && trim($s) !== '');
            foreach ($sizes as $size) {
                $product->tailles()->create(['label_taille' => $size]);
            }
        }
    
        return redirect()->route('make_product.create')
                         ->with('success', 'Produit créé avec succès !');
    }
}
