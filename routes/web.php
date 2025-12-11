<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NationController;
use App\Http\Controllers\PanierController; // Importé une seule fois ici
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RegisterProfessionalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProduitProposeController; 
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\SalesController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- PAGES STATIQUES & VUES SIMPLES ---
Route::get('/', function () {
    return view('welcome');
});
Route::get('/creer_un_produit', function () {
    return view('product_creation');
});
/*
Route::get('/parametre_compte', function () {
    return view('account_modification');
});
*/
Route::get('/se_connecter', function () {
    return view('account_connection');
});

Route::get('/vote', function () {
    return view('vote_fifa');
});

Route::get('/privacy_policy', function () {
    return view('privacy_policy');
});

Route::get('/players', function () {
    return view('players');
});

Route::get('/commande', function () {
    return view('vue_commande');
});


// --- BOTMAN ---
Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle']);

// --- PRODUITS ---
Route::get('/produits', [ProductController::class, 'index']);
Route::get('/produit/{id}', [ProductController::class, 'detail'])->name('product.detail');

// --- AUTHENTIFICATION (INSCRIPTION) ---
// Particulier
Route::get('/creer_un_compte_1', [RegisterController::class, 'step1'])->name('register.step1');
Route::post('/creer_un_compte_1', [RegisterController::class, 'step1Post'])->name('register.step1.post');
Route::get('/creer_un_compte_2', [RegisterController::class, 'step2'])->name('register.step2');
Route::post('/creer_un_compte_2', [RegisterController::class, 'step2Post'])->name('register.step2.post');

// Professionnel

Route::get('/creer_un_compte_professionnel_1', [RegisterProfessionalController::class, 'step1'])->name('registerPro.step1');
Route::post('/creer_un_compte_professionnel_1', [RegisterProfessionalController::class, 'step1Post'])->name('registerPro.step1.post');
Route::get('/creer_un_compte_professionnel_2', [RegisterProfessionalController::class, 'step2'])->name('registerPro.step2');
Route::post('/creer_un_compte_professionnel_2', [RegisterProfessionalController::class, 'step2Post'])->name('registerPro.step2.post');

// --- AUTHENTIFICATION (CONNEXION) ---
Route::get('/connexion', [LoginController::class, 'formulaire'])->name('login');
Route::post('/connexion', [LoginController::class, 'traitement']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');






Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');

Route::get('/panier/ajouter/{id_produit}', [PanierController::class, 'ajouter'])->name('panier.ajouter');

Route::get('/panier/supprimer/{id_ligne}', [PanierController::class, 'supprimer'])->name('panier.supprimer');

Route::get('/panier/update/{id_ligne}/{action}', [PanierController::class, 'updateQuantite'])->name('panier.update');


// Route protégée (il faut être connecté) pour voir son profil
Route::get('/mon-profil', [ProfileController::class, 'show'])->middleware('auth')->name('profile.show');
// Modification (Affichage du formulaire)
Route::get('/parametre_compte', [ProfileController::class, 'edit'])->middleware('auth')->name('profile.edit');
Route::put('/parametre_compte', [ProfileController::class, 'update'])->name('profile.update');
// Modification (Traitement du formulaire)
/*Route::post('/parametre_compte', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');*/

Route::get('/statistiques_de_ventes', function(){
    return view('statistique');
});
    
Route::get('/statistiques_de_ventes', [SalesController::class, 'index']);

Route::get('/proposer_un_produit', function (){
    return view('product_demand');
});


Route::get('/proposer_un_produit', [ProduitProposeController::class, 'step1'])->name('registerProduct.step1');
Route::post('/proposer_un_produit', [ProduitProposeController::class, 'step1Post'])->name('registerProduct.step1.post');


Route::post('/commande/valider', [CommandeController::class, 'valider'])->name('commande.valider');



Route::get('/commande', [CommandeController::class, 'afficher'])
    ->middleware('auth')
    ->name('commande.page');

Route::post('/commande/valider', [CommandeController::class, 'valider'])
    ->name('commande.valider');

Route::get('/commande/confirmation', [CommandeController::class, 'confirmation'])
    ->name('commande.confirmation');

    use App\Http\Controllers\MakeProductController;

// Afficher le formulaire de création de produit
Route::get('/produit/creer', [MakeProductController::class, 'create'])
    ->name('make_product.create');

// Traiter le formulaire (sauvegarde en base)
Route::post('/produit/creer', [MakeProductController::class, 'store'])
    ->name('make_product.store');