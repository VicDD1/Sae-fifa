<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User_connecte as User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Acheteur;


class RegisterController extends Controller
{
    
    public function step1()
    {
        return view('account_creation');
        
    }

    
    public function step1Post(Request $request)
    {
       
        $request->validate([
            'prenom_user_connecte' => 'required',
            'courriel_user_connecte' => 'required|email|unique:user_connecte,courriel_user_connecte',
            'surnom_user_connecte' => 'nullable|string',
            'date_de_naissance_user_connecte' => ['required', 'before_or_equal:' .  Date('Y-m-d')],
            'pays_de_naissance_user_connecte' => 'required',
            'favori_user_connecte' => 'required',
            'langue_user_connecte' => 'required',
        ],[
            'courriel_user_connecte.unique' => "Cette adresse e-mail est déjà utilisé. Veuillez en choisir un autre.",
            'courriel_user_connecte.email' => "Veuillez saisir une adresse e-mail valide.",
            'date_de_naissance_user_connecte.before_or_equal' => "La date de naissance doit être aujourd’hui ou une date antérieure.",
        ]);

        session([
            'register' => $request->only(
                'prenom_user_connecte',
                'courriel_user_connecte',
                'surnom_user_connecte',
                'date_de_naissance_user_connecte',
                'pays_de_naissance_user_connecte',
                'favori_user_connecte',
                'langue_user_connecte',
            )
        ]);

        return redirect()->route('register.step2');
    }
    public function step2()
    {
        if (!session()->has('register')) {
            return redirect()->route('register.step1');
        }

        return view('account_creation_mdp');
    }
        public function step2Post(Request $request)
        {
            $request->validate([
                'password_user_connecte' => 'required|min:6|confirmed',
            ],[
                'password_user_connecte.min' => "Le mot de passe doit contenir au moins 6 caractères.",
                'password_user_connecte.confirmed' => "Les mots de passe ne correspondent pas.",
            ]);
            

            $data = session('register');
            if (!$data) {
                
                return redirect()->route('register.step1')
                                ->with('error', 'Veuillez remplir la première étape du formulaire.');
            }
        
            $user = User::create([
                'prenom_user_connecte' => $data['prenom_user_connecte'],
                'courriel_user_connecte' => $data['courriel_user_connecte'],
                'surnom_user_connecte' => $data['surnom_user_connecte'] ?? null,
                'date_de_naissance_user_connecte' => $data['date_de_naissance_user_connecte'],
                'pays_de_naissance_user_connecte' => $data['pays_de_naissance_user_connecte'],
                'favori_user_connecte' => $data['favori_user_connecte'],
                'langue_user_connecte' => $data['langue_user_connecte'],
                'password_user_connecte' => Hash::make($request->password_user_connecte),
                
            ]);

            Acheteur::create([
                'id_user_connecte' => $user->id_user_connecte,
                'id_acheteur' => $user->id_user_connecte,
                'prenom_user_connecte' => $data['prenom_user_connecte'],
                'courriel_user_connecte' => $data['courriel_user_connecte'],
                'surnom_user_connecte' => $data['surnom_user_connecte'] ?? null,
                'date_de_naissance_user_connecte' => $data['date_de_naissance_user_connecte'],
                'pays_de_naissance_user_connecte' => $data['pays_de_naissance_user_connecte'],
                'favori_user_connecte' => $data['favori_user_connecte'],
                'langue_user_connecte' => $data['langue_user_connecte'],
            ]);
            
           
            $redirect = session('redirect_after_register');
            Auth::login($user);
            session()->forget('register');
            if ($redirect) {
                return redirect($redirect)->with('success', 'Votre compte a été créé. Vous pouvez maintenant créer un compte professionnel.');
            }
            session()->forget('redirect_after_register');
            return redirect('/')->with('success', 'Votre compte a été créé!');
            
        }
 

    }





