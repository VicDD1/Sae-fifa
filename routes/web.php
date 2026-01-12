<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Categorie_ProduitControler;
use App\Http\Controllers\NationController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RegisterProfessionalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProduitProposeController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\MakeProductController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\MfaController;
use App\Http\Controllers\BlogController;
  Route::post('/botman', [App\Http\Controllers\BotManController::class, 'handle']);
  Route::get('/cookies', function () {
      return view('voir_cookies');
  })->name('cookies.manage');
  /*
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

Route::get('/statistiques_de_ventes', [SalesController::class, 'index']);
Route::get('/localisation_des_ventes', [SalesController::class, 'showSalesMap']);
Route::get('/produits_en_cours',[ProductController::class,'incomplet']);
Route::get('/produit_en_cours/{id}', [ProductController::class, 'modify'])->name('product.modify');
Route::post('/produit/{id}/valider', [ProductController::class, 'validateProduct'])
    ->name('product.validate');
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




Route::get('/produits', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/creer_categorie', [Categorie_ProduitControler::class, 'create'])->name('categorie.create');
Route::post('/categorie_store', [Categorie_ProduitControler::class, 'store'])->name('categorie.store');

Route::get('/produits', [ProductController::class, 'index'])->name('product.index');
Route::get('/produit/{id}', [ProductController::class, 'detail'])
->whereNumber('id')
->name('product.detail');

Route::get('/produits/creer', [ProductController::class, 'create'])->name('make_product.create');
Route::post('/produits', [ProductController::class, 'store'])->name('make_product.store');
Route::get('/produits', [ProductController::class, 'index'])->name('product.index');

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

    // Page commande (adresse + livraison)
    Route::get('/commande', [CommandeController::class, 'afficher'])
        ->middleware('auth')
        ->name('commande.page');

    // GET: ré-affiche la page de confirmation si les données checkout existent (sinon retour /commande)
    Route::get('/confirmation_commande', [CommandeController::class, 'confirmationPage'])
        ->middleware('auth');

    // POST: validation -> affiche la page de confirmation (récap + formulaire CB)
    Route::post('/confirmation_commande', [CommandeController::class, 'valider'])
        ->middleware('auth')
        ->name('commande.valider');


    // POST: action finale (paiement simulé + création commande)
    Route::post('/succes_commande', [CommandeController::class, 'confirmation'])
        ->middleware('auth')
        ->name('commande.payer');

    // GET: page succès (affichage)
    Route::get('/succes_commande', [CommandeController::class, 'succes'])
        ->middleware('auth')    
        ->name('commande.succes');

    // Liste commandes
    Route::get('/mes_commandes', [CommandeController::class, 'liste'])
        ->middleware('auth')
        ->name('commande.liste');


    // Formulaire de vote
    Route::get('/vote/fifa', [VoteController::class, 'votePage'])
        ->name('vote.page');
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

    // Routes pour la réinitialisation de mot de passe
    Route::get('/oubli-mdp', [ResetPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/oubli-mdp', [ResetPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-mdp/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-mdp', [ResetPasswordController::class, 'resetPassword'])->name('password.update');

// --- Routes pour la Double Authentification (MFA) ---

// 1. Pour activer l'option depuis le profil (il faut être connecté)
Route::post('/profil/activer-mfa', [MfaController::class, 'enableMfa'])
    ->name('mfa.enable')
    ->middleware('auth');

// 2. Pour afficher la page "Entrez le code" (lors de la connexion)
Route::get('/login/mfa', [MfaController::class, 'showMfaForm'])
    ->name('mfa.form');

// 3. Pour valider le code et se connecter
Route::post('/login/mfa', [MfaController::class, 'verifyMfa'])
    ->name('mfa.verify');


// -- SECTION BLOG --
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blog.show');

// Route protégée (il faut être connecté pour commenter)
Route::post('/blog/{id}/comment', [BlogController::class, 'storeComment'])
    ->middleware('auth')
    ->name('blog.comment.store');