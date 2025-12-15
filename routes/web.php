<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\BotManController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NationController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RegisterProfessionalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProduitProposeController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\VoteController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'));
Route::get('/se_connecter', fn() => view('account_connection'));
Route::get('/vote', fn() => view('vote_fifa'));
Route::get('/privacy_policy', fn() => view('privacy_policy'));
Route::get('/players', fn() => view('players'));

/* ------------------------------
   Pages statiques & simples
------------------------------ */
Route::get('/produit/stock', [ProductController::class, 'getStock']);

/* ------------------------------
   Pages statiques & simples
------------------------------ */

Route::get('/', function () {
    return view('welcome');
});

Route::view('/parametre_compte', 'account_modification');
Route::view('/se_connecter', 'account_connection');
Route::view('/privacy_policy', 'privacy_policy');
Route::view('/players', 'players');
Route::view('/commande', 'vue_commande');

/* ------------------------------
   BotMan
------------------------------ */
Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle']);

/* ------------------------------
   Produits
------------------------------ */
Route::get('/produits', [ProductController::class, 'index']);
Route::get('/produit/{id}', [ProductController::class, 'detail'])->name('product.detail');

/* ------------------------------
   Authentification — Inscription
------------------------------ */
// Particulier
Route::get('/creer_un_produit', function () {
    return view('product_creation');
});
/*
|--------------------------------------------------------------------------
| PRODUITS
|--------------------------------------------------------------------------
*/
Route::get('/produits', [ProductController::class, 'index']);
Route::get('/produit/{id}', [ProductController::class, 'detail'])->name('product.detail');

/* ------------------------------
   Authentification — Inscription
------------------------------ */
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

/* ------------------------------
   Connexion & Déconnexion
------------------------------ */
Route::get('/connexion', [LoginController::class, 'formulaire'])->name('login');
Route::post('/connexion', [LoginController::class, 'traitement']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/* ------------------------------
   Panier
------------------------------ */
Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
Route::get('/panier/ajouter/{id_produit}', [PanierController::class, 'ajouter'])->name('panier.ajouter');
Route::get('/panier/supprimer/{id_ligne}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
Route::get('/panier/update/{id_ligne}/{action}', [PanierController::class, 'updateQuantite'])->name('panier.update');

/* ------------------------------
   Profil utilisateur (protégé)
------------------------------ */
Route::get('/mon-profil', [ProfileController::class, 'show'])
    ->middleware('auth')
    ->name('profile.show');

Route::get('/parametre_compte', [ProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('profile.edit');

Route::post('/parametre_compte', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

/* ------------------------------
   Proposer un produit
------------------------------ */
Route::get('/proposer_un_produit', [ProduitProposeController::class, 'step1'])
    ->name('registerProduct.step1');

Route::post('/proposer_un_produit', [ProduitProposeController::class, 'step1Post'])
    ->name('registerProduct.step1.post');

/* ------------------------------
   Commandes
------------------------------ */
Route::post('/commande/valider', [CommandeController::class, 'valider'])->name('commande.valider');

Route::get('/commande', [CommandeController::class, 'afficher']);
/*
|--------------------------------------------------------------------------
| PROPOSITION PRODUIT
|--------------------------------------------------------------------------
*/
Route::get('/proposer_un_produit', [ProduitProposeController::class, 'step1'])->name('registerProduct.step1');
Route::post('/proposer_un_produit', [ProduitProposeController::class, 'step1Post'])->name('registerProduct.step1.post');

Route::post('/parametre_compte', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

/* ------------------------------
   Proposer un produit
------------------------------ */
Route::get('/proposer_un_produit', [ProduitProposeController::class, 'step1'])
    ->name('registerProduct.step1');

Route::post('/proposer_un_produit', [ProduitProposeController::class, 'step1Post'])
    ->name('registerProduct.step1.post');

/* ------------------------------
   Commandes
------------------------------ */
Route::post('/commande/valider', [CommandeController::class, 'valider'])->name('commande.valider');

Route::get('/commande', [CommandeController::class, 'afficher'])
    ->middleware('auth')
    ->name('commande.page');

Route::get('/commande/confirmation', [CommandeController::class, 'confirmation'])
    ->name('commande.confirmation');

/* ------------------------------
   VOTE FIFA (FINAL, PROPRE)
------------------------------ */


// Formulaire de vote
Route::get('/vote/fifa', [VoteController::class, 'votePage'])
    ->name('vote.page');

// Soumission du vote (POST uniquement)
Route::post('/vote/submit', [VoteController::class, 'submit'])
    ->middleware('auth')
    ->name('commande.succes');

Route::get('/mes_commandes', [CommandeController::class, 'liste'])
    ->middleware('auth')
    ->name('commande.liste');

Route::get('/commande/confirmation', [CommandeController::class, 'confirmation'])
    ->name('commande.confirmation');

/* ------------------------------
   VOTE FIFA (FINAL, PROPRE)
------------------------------ */

// Affichage du formulaire de vote
Route::get('/vote/fifa', [VoteController::class, 'votePage'])
    ->name('vote.page');

// Soumission du vote (doit être connecté)
Route::post('/vote/submit', [VoteController::class, 'submit'])
    ->middleware('auth')
    ->name('vote.submit');

// Page de récapitulatif (GET)
Route::get('/vote/recap', [VoteController::class, 'recap'])
    ->middleware('auth')
    ->name('vote.recap');

