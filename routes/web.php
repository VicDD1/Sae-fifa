<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RegisterProfessionalController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BotManController;
use App\Http\Controllers\Categorie_ProduitControler;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\ExpeditionController;
use App\Http\Controllers\GestionCommandeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MakeProductController;
use App\Http\Controllers\MfaController;
use App\Http\Controllers\NationController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProduitProposeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RGPDController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\Theme_VoteController;
use App\Http\Controllers\VoteController;

/*
|--------------------------------------------------------------------------
| PARTIE : RGPD & COOKIES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/gestion-rgpd', [RGPDController::class, 'index'])->name('rgpd.gestion');
    Route::post('/anonymize-donnees', [RGPDController::class, 'anonymize'])->name('rgpd.anonymize');
});

Route::get('/cookies', function () {
    return view('voir_cookies');
})->name('cookies.manage');

Route::get('/delete', [ProfileController::class, 'delete'])->name('user.delete');
Route::get('/anonymize', [ProfileController::class, 'anonime'])->name('user.anonime');


/*
|--------------------------------------------------------------------------
| PARTIE : GESTION COMMANDE (SIÈGE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('siege')->group(function () {
    Route::get('/commandes', [GestionCommandeController::class, 'index'])
        ->name('siege.commandes.index');

    Route::post('/commandes/{id}/update-statut', [GestionCommandeController::class, 'updateStatut'])
        ->name('siege.commandes.update');

    Route::get('/commandes/rapport-express', [GestionCommandeController::class, 'rapportQualite'])
        ->name('siege.commandes.qualite');
});


/*
|--------------------------------------------------------------------------
| PARTIE : PAGES WEB & STATIQUES
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index']);
Route::get('/se_connecter', fn() => view('account_connection'));
Route::get('/vote', fn() => view('vote_fifa'));
Route::get('/privacy_policy', fn() => view('privacy_policy'));
Route::get('/players', fn() => view('players'));

// Doublons / Variantes de vues
Route::view('/parametre_compte', 'account_modification');
Route::view('/commande', 'vue_commande');


/*
|--------------------------------------------------------------------------
| PARTIE : PRODUITS & CATÉGORIES
|--------------------------------------------------------------------------
*/
Route::get('/produit/stock', [ProductController::class, 'getStock']);
Route::get('/produits_en_cours',[ProductController::class,'incomplet']);
Route::get('/produit_en_cours/{id}', [ProductController::class, 'modify'])->name('product.modify');
Route::post('/produit/{id}/valider', [ProductController::class, 'validateProduct'])->name('product.validate');

Route::get('/produit/{id}', [ProductController::class, 'detail'])->name('product.detail');


Route::get('/produits', [ProductController::class, 'index'])->name('product.index');

Route::get('/produits/creer', [ProductController::class, 'create'])->name('make_product.create');
Route::post('/produits', [ProductController::class, 'store'])->name('make_product.store');

Route::get('/creer_categorie', [Categorie_ProduitControler::class, 'create'])->name('categorie.create');
Route::post('/categorie_store', [Categorie_ProduitControler::class, 'store'])->name('categorie.store');


/*
|--------------------------------------------------------------------------
| PARTIE : VENTES & STATISTIQUES
|--------------------------------------------------------------------------
*/
Route::get('/statistiques_de_ventes', [SalesController::class, 'index']);
Route::get('/localisation_des_ventes', [SalesController::class, 'showSalesMap']);


/*
|--------------------------------------------------------------------------
| PARTIE : BOTMAN
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle']);


/*
|--------------------------------------------------------------------------
| PARTIE : EXPÉDITION (SERVICE VENTE)
|--------------------------------------------------------------------------
*/
Route::get('/service-vente/commandes', [ExpeditionController::class, 'index'])->middleware('auth')->name('service_vente.commandes');
Route::post('/service-vente/commandes/{id}/valider', [ExpeditionController::class, 'validerEnlevement'])->middleware('auth')->name('expedition.valider');
Route::get('/service-vente/livraisons-demain', [App\Http\Controllers\ExpeditionController::class, 'livraisonsDemain'])->middleware('auth')->name('expedition.demain');
Route::get('/service-vente/historique', [App\Http\Controllers\ExpeditionController::class, 'historique'])->middleware('auth')->name('expedition.historique');
Route::get('/service-vente/livraisons-domicile-proche', [App\Http\Controllers\ExpeditionController::class, 'livraisonsDomicileProche'])->middleware('auth')->name('expedition.domicile_proche');


/*
|--------------------------------------------------------------------------
| PARTIE : AUTHENTIFICATION (INSCRIPTION / CONNEXION / OTP)
|--------------------------------------------------------------------------
*/
// Inscription Particulier
Route::get('/creer_un_compte_1', [RegisterController::class, 'step1'])->name('register.step1');
Route::post('/creer_un_compte_1', [RegisterController::class, 'step1Post'])->name('register.step1.post');
Route::get('/creer_un_compte_2', [RegisterController::class, 'step2'])->name('register.step2');
Route::post('/creer_un_compte_2', [RegisterController::class, 'step2Post'])->name('register.step2.post');

// Inscription Pro
Route::get('/creer_un_compte_professionnel_1', [RegisterProfessionalController::class, 'step1'])->name('registerPro.step1');
Route::post('/creer_un_compte_professionnel_1', [RegisterProfessionalController::class, 'step1Post'])->name('registerPro.step1.post');
Route::get('/creer_un_compte_professionnel_2', [RegisterProfessionalController::class, 'step2'])->name('registerPro.step2');
Route::post('/creer_un_compte_professionnel_2', [RegisterProfessionalController::class, 'step2Post'])->name('registerPro.step2.post');

// Connexion
Route::get('/connexion', [LoginController::class, 'formulaire'])->name('login');
Route::post('/connexion', [LoginController::class, 'traitement']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Reset Password
Route::get('/oubli-mdp', [ResetPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/oubli-mdp', [ResetPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-mdp/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-mdp', [ResetPasswordController::class, 'resetPassword'])->name('password.update');

// MFA
Route::post('/profil/activer-mfa', [MfaController::class, 'enableMfa'])->name('mfa.enable')->middleware('auth');
Route::get('/login/mfa', [MfaController::class, 'showMfaForm'])->name('mfa.form');
Route::post('/login/mfa', [MfaController::class, 'verifyMfa'])->name('mfa.verify');


/*
|--------------------------------------------------------------------------
| PARTIE : PROFIL & PANIER
|--------------------------------------------------------------------------
*/
// Panier
Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
Route::get('/panier/ajouter/{id_produit}', [PanierController::class, 'ajouter'])->name('panier.ajouter');
Route::get('/panier/supprimer/{id_ligne}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
Route::get('/panier/update/{id_ligne}/{action}', [PanierController::class, 'updateQuantite'])->name('panier.update');

// Profil
Route::get('/mon-profil', [ProfileController::class, 'show'])->middleware('auth')->name('profile.show');
Route::get('/parametre_compte', [ProfileController::class, 'edit'])->middleware('auth')->name('profile.edit');
Route::post('/parametre_compte', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');

/*
|--------------------------------------------------------------------------
| PARTIE : PROPOSITION PRODUIT
|--------------------------------------------------------------------------
*/
Route::get('/proposer_un_produit', [ProduitProposeController::class, 'step1'])->name('registerProduct.step1');
Route::post('/proposer_un_produit', [ProduitProposeController::class, 'step1Post'])->name('registerProduct.step1.post');

/*
|--------------------------------------------------------------------------
| PARTIE : COMMANDES CLIENTS
|--------------------------------------------------------------------------
*/
Route::get('/commande', [CommandeController::class, 'afficher'])->middleware('auth')->name('commande.page');
Route::get('/confirmation_commande', [CommandeController::class, 'confirmationPage'])->middleware('auth');
Route::post('/confirmation_commande', [CommandeController::class, 'valider'])->middleware('auth')->name('commande.valider');
Route::post('/succes_commande', [CommandeController::class, 'confirmation'])->middleware('auth')->name('commande.payer');
Route::get('/succes_commande', [CommandeController::class, 'succes'])->middleware('auth')->name('commande.succes');
Route::get('/mes_commandes', [CommandeController::class, 'liste'])->middleware('auth')->name('commande.liste');


/*
|--------------------------------------------------------------------------
| PARTIE : VOTE FIFA
|--------------------------------------------------------------------------
*/
Route::get('/vote/fifa', [VoteController::class, 'votePage'])->name('vote.page');
Route::post('/vote/submit', [VoteController::class, 'submit'])->middleware('auth')->name('vote.submit');
Route::get('/vote/recap', [VoteController::class, 'recap'])->middleware('auth')->name('vote.recap');


/*
|--------------------------------------------------------------------------
| PARTIE : STRIPE & BLOG
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent'])->name('stripe.createPaymentIntent');
    Route::post('/stripe/confirm-payment', [StripeController::class, 'confirmPayment'])->name('stripe.confirmPayment');
    Route::get('/stripe/saved-cards', [StripeController::class, 'getSavedCards'])->name('stripe.savedCards');
    Route::post('/stripe/pay-with-saved-card', [StripeController::class, 'payWithSavedCard'])->name('stripe.payWithSavedCard');
});

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{id}/comment', [BlogController::class, 'storeComment'])->middleware('auth')->name('blog.comment.store');
Route::delete('/blog/comment/{id}', [BlogController::class, 'destroyComment'])->middleware('auth')->name('blog.comment.destroy');


/*
|--------------------------------------------------------------------------
| PARTIE : GESTION DES THEMES DE VOTE
|--------------------------------------------------------------------------
*/
Route::get('/themes-vote', [Theme_VoteController::class, 'index'])->name('theme_vote.index');
Route::get('/themes-vote/creer', [Theme_VoteController::class, 'create'])->name('theme_vote.create');
Route::post('/themes-vote', [Theme_VoteController::class, 'store'])->name('theme_vote.store');
Route::get('/themes-vote/{id}', [Theme_VoteController::class, 'show'])->name('theme_vote.show');
Route::post('/themes-vote/{id}/joueurs', [Theme_VoteController::class, 'associerJoueurs'])->name('theme_vote.associer_joueurs');
Route::delete('/themes-vote/{idTheme}/joueurs/{idJoueur}', [Theme_VoteController::class, 'retirerJoueur'])->name('theme_vote.retirer_joueur');
Route::delete('/themes-vote/{id}', [Theme_VoteController::class, 'destroy'])->name('theme_vote.destroy');
