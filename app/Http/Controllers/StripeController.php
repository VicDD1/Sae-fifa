<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\PaymentMethod;

use App\Models\Panier;
use App\Models\Commande;
use App\Models\Ligne_commande;
use App\Models\Adresse;
use App\Models\Acheteur;
use App\Models\Mode_livraison;
use App\Models\Ligne_panier;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Créer un PaymentIntent pour le paiement
     */
    public function createPaymentIntent(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $panier = Panier::where('id_user_connecte', Auth::id())->first();
        if (!$panier || $panier->lignes->count() === 0) {
            return response()->json(['error' => 'Panier vide'], 400);
        }

        $lignes = $panier->lignes;
        $total = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);

        $modeId = $request->input('mode_livraison');
        $mode = Mode_livraison::find($modeId);
        $fraisLivraison = $mode ? $mode->prix_mode_livraison : 0;

        $totalFinal = $total + $fraisLivraison;

        // Stripe utilise les centimes
        $amount = (int) round($totalFinal * 100);

        try {
            // Créer ou récupérer le customer Stripe
            $user = Auth::user();
            $customerId = $this->getOrCreateStripeCustomer($user);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'eur',
                'customer' => $customerId,
                'setup_future_usage' => 'on_session', // Sauvegarde automatique de la carte chez Stripe
                'metadata' => [
                    'user_id' => Auth::id(),
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $totalFinal,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Créer ou récupérer le customer Stripe
     */
    private function getOrCreateStripeCustomer($user)
    {
        // Vérifier si l'utilisateur a déjà un stripe_customer_id
        if ($user->stripe_customer_id) {
            return $user->stripe_customer_id;
        }

        // Créer un nouveau customer
        $customer = Customer::create([
            'email' => $user->courriel_user_connecte ?? $user->email,
            'name' => $user->prenom_user_connecte . ' ' . ($user->nom_user_connecte ?? ''),
            'metadata' => [
                'user_id' => $user->id_user_connecte,
            ],
        ]);

        // Sauvegarder le stripe_customer_id
        $user->stripe_customer_id = $customer->id;
        $user->save();

        return $customer->id;
    }

    /**
     * Confirmer le paiement et créer la commande
     */
    public function confirmPayment(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $request->validate([
            'payment_intent_id' => 'required|string',
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'cp' => 'required|string|max:20',
            'telephone' => 'required|string|max:20',
            'mode_livraison' => 'required|integer|exists:mode_livraison,id_mode_livraison',
        ]);

        try {
            // Vérifier le statut du paiement
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json(['error' => 'Le paiement n\'a pas été confirmé'], 400);
            }

            $panier = Panier::where('id_user_connecte', Auth::id())->firstOrFail();
            $lignes = $panier->lignes;
            $total = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);

            $mode = Mode_livraison::findOrFail($request->mode_livraison);
            $totalFinal = $total + $mode->prix_mode_livraison;

            // Créer l'acheteur si nécessaire
            $acheteur = Acheteur::where('id_user_connecte', Auth::id())->first();
            if (!$acheteur) {
                $acheteur = Acheteur::create([
                    'id_user_connecte' => Auth::id(),
                    'telephone_acheteur' => $request->telephone,
                    'adresse_livraison' => $request->adresse,
                ]);
            }

            // Créer l'adresse
            $adresse = Adresse::create([
                'pays_adresse' => $request->pays ?? 'France',
                'code_postal' => $request->cp,
                'ville_adresse' => $request->ville,
            ]);

            // Créer la commande
            $commande = Commande::create([
                'id_adresse' => $adresse->id_adresse,
                'id_user_connecte' => Auth::id(),
                'id_acheteur' => $acheteur->id_acheteur,
                'id_mode_livraison' => $mode->id_mode_livraison,
                'date_commande' => now(),
                'montant_total' => $totalFinal,
                'date_paiement' => now(),
                'mode_paiement' => 'carte',
                'statut_paiement' => 'Paye',
            ]);

            // Créer les lignes de commande
            foreach ($lignes as $ligne) {
                Ligne_commande::create([
                    'id_commande' => $commande->id_commande,
                    'id_produit' => $ligne->id_produit,
                    'id_colori' => $ligne->id_colori,
                    'id_taille' => $ligne->id_taille,
                    'quantitee' => $ligne->quantitee,
                ]);
            }

            // Vider le panier
            Ligne_panier::where('id_panier', $panier->id_panier)->delete();

            return response()->json([
                'success' => true,
                'redirect' => route('commande.succes'),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Récupérer les cartes sauvegardées de l'utilisateur depuis Stripe
     */
    public function getSavedCards()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $user = Auth::user();

        if (!$user->stripe_customer_id) {
            return response()->json(['cards' => []]);
        }

        try {
            // Récupérer les cartes directement depuis Stripe
            $paymentMethods = PaymentMethod::all([
                'customer' => $user->stripe_customer_id,
                'type' => 'card',
            ]);

            $cards = collect($paymentMethods->data)->map(function ($pm) {
                return [
                    'id' => $pm->id,
                    'brand' => strtoupper($pm->card->brand),
                    'last4' => $pm->card->last4,
                    'exp_month' => $pm->card->exp_month,
                    'exp_year' => $pm->card->exp_year,
                ];
            });

            return response()->json(['cards' => $cards]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Payer avec une carte sauvegardée chez Stripe
     */
    public function payWithSavedCard(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $request->validate([
            'payment_method_id' => 'required|string',
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'cp' => 'required|string|max:20',
            'telephone' => 'required|string|max:20',
            'mode_livraison' => 'required|integer|exists:mode_livraison,id_mode_livraison',
        ]);

        $panier = Panier::where('id_user_connecte', Auth::id())->first();
        if (!$panier || $panier->lignes->count() === 0) {
            return response()->json(['error' => 'Panier vide'], 400);
        }

        $lignes = $panier->lignes;
        $total = $lignes->sum(fn($l) => $l->quantitee * $l->produit->prix_base);

        $mode = Mode_livraison::findOrFail($request->mode_livraison);
        $totalFinal = $total + $mode->prix_mode_livraison;
        $amount = (int) round($totalFinal * 100);

        try {
            $user = Auth::user();

            // Créer et confirmer le paiement avec la carte sauvegardée
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'eur',
                'customer' => $user->stripe_customer_id,
                'payment_method' => $request->payment_method_id,
                'off_session' => false,
                'confirm' => true,
                'return_url' => route('commande.succes'),
            ]);

            if ($paymentIntent->status === 'succeeded') {
                // Créer l'acheteur si nécessaire
                $acheteur = Acheteur::where('id_user_connecte', Auth::id())->first();
                if (!$acheteur) {
                    $acheteur = Acheteur::create([
                        'id_user_connecte' => Auth::id(),
                        'telephone_acheteur' => $request->telephone,
                        'adresse_livraison' => $request->adresse,
                    ]);
                }

                // Créer l'adresse
                $adresse = Adresse::create([
                    'pays_adresse' => $request->pays ?? 'France',
                    'code_postal' => $request->cp,
                    'ville_adresse' => $request->ville,
                ]);

                // Créer la commande
                $commande = Commande::create([
                    'id_adresse' => $adresse->id_adresse,
                    'id_user_connecte' => Auth::id(),
                    'id_acheteur' => $acheteur->id_acheteur,
                    'id_mode_livraison' => $mode->id_mode_livraison,
                    'date_commande' => now(),
                    'montant_total' => $totalFinal,
                    'date_paiement' => now(),
                    'mode_paiement' => 'carte',
                    'statut_paiement' => 'Paye',
                ]);

                // Créer les lignes de commande
                foreach ($lignes as $ligne) {
                    Ligne_commande::create([
                        'id_commande' => $commande->id_commande,
                        'id_produit' => $ligne->id_produit,
                        'id_colori' => $ligne->id_colori,
                        'id_taille' => $ligne->id_taille,
                        'quantitee' => $ligne->quantitee,
                    ]);
                }

                // Vider le panier
                Ligne_panier::where('id_panier', $panier->id_panier)->delete();

                return response()->json([
                    'success' => true,
                    'redirect' => route('commande.succes'),
                ]);
            }

            return response()->json(['error' => 'Paiement non confirmé'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
