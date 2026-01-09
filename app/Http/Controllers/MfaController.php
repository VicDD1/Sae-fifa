<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User_connecte;

class MfaController extends Controller
{
    // 1. Activer le MFA (Version compatible avec le Sélecteur de Pays)
    public function enableMfa(Request $request)
{
    $user = Auth::user();

        // A. Validation : On attend DEUX champs (l'indicatif et le reste)
        $request->validate([
            'indicatif' => 'required|string',     // Ex: "+33"
            'numero_suffixe' => 'required',       // Ex: "0612345678"
        ]);

        // B. Nettoyage : On ne garde que les chiffres pour le suffixe
        $suffixeNettoye = preg_replace('/[^0-9]/', '', $request->numero_suffixe);

        // C. On enlève le "0" du début s'il y en a un
        // Ex: "061234..." devient "61234..."
        $suffixeSansZero = ltrim($suffixeNettoye, '0');

        // D. Assemblage Final : "+33" + "61234..."
        $numeroComplet = $request->indicatif . $suffixeSansZero;

        // E. Vérification de longueur (sécurité)
        if (strlen($numeroComplet) < 8 || strlen($numeroComplet) > 16) {
            return back()->withErrors(['numero_suffixe' => 'Le numéro semble invalide.']);
        }

        // F. Sauvegarde
        $user->update([
            'numero_telephone_user_connecte' => $numeroComplet,
            'mfa_active' => true 
        ]);

        return back()->with('success', 'Sécurité activée sur le numéro : ' . $numeroComplet);
    }

    // 2. Afficher la page du code (Inchangé)
    public function showMfaForm()
    {
        if (!session()->has('mfa_user_id')) {
            return redirect('/connexion');
        }
        return view('auth.mfa_verify');
    }

    // 3. Vérifier le code (Inchangé)
    public function verifyMfa(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $userId = session('mfa_user_id');
        $user = User_connecte::find($userId);

        if (!$user) {
            return redirect('/connexion')->with('error', 'Session expirée.');
        }

        if ($user->mfa_code === $request->code && Carbon::now()->lessThan($user->mfa_expiration)) {
            Auth::login($user);
            session()->forget('mfa_user_id');
            $user->update(['mfa_code' => null, 'mfa_expiration' => null]);
            return redirect('/')->with('success', 'Connexion réussie !');
        }

        return back()->with('error', 'Code invalide.');
    }
}