<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\Professionnel as Pro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Guard;
class RegisterProfessionalController extends Controller
{
    
    public function step1()
    {
        $this->current_user =Auth::user();
        
        if (is_null($this->current_user))
        {
            session(['redirect_after_register' => route('registerPro.step1')]);
            return redirect('/creer_un_compte_1')
            ->with('error', 'Vous devez créer un compte avant de créer un compte professionnel.'); };
        return view('pro_account_creation');
        
    }

   
    public function step1Post(Request $request)
    {
        $fullPhone = $request->input('telephone_country_code') . preg_replace('/\s+/', '', $request->input('telephone_local'));
       
        $request->validate([
            'nom_societe' => 'required|min:2|max:50',
            'numero_TVA' => 'required|unique:professionnel,numero_tva|regex:/^[A-Z]{2}[0-9A-Za-z]{8,12}$/',
            'activite_professionnel'=>'required|max:50',
            'email_professionnel' => 'required|email:dns|unique:professionnel,email_professionnel|max:50',
            'nom_professionnel' => 'required|string|max:50',
            'prenom_professionnel' => 'required|string|max:50',
            'adresse_professionnel' => 'required|max:50',
            'code_postal_professionnel' => ['required','regex:/^[A-Za-z0-9\- ]{3,10}$/'],
            'pays_professionnel' => 'required|max:50',
            'ville_professionnel' => 'required|max:70',
            'telephone_professionnel' => ['required','regex:/^(?:\+33|0)[1-9](?:[\s.-]?\d{2}){4}$/'],
        ],[
            'numero_TVA.regex'=>"ce format n'est pas valide",
            'nom_societe.max'=>"le nom ne peut pas depasser 50 caracteres",
            'numero_TVA.integer'=>"le numero TVA doit etre un nombre",
            'numero_TVA.max'=>"le numero TVA ne peut pas depasser 50 caracteres",
            'numero_TVA.unique'=>"ce numero TVA est deja utilisé",
            'activite_professionnel.max'=>"l'activité professionnel ne peut pas depasser 50 caracteres",
            'email_professionnel.email'=>"l'adresse email n'est pas valide",
            'email_professionnel.max'=>"l'adresse email ne peut pas depasser 50 caracteres",
            'email_professionnel.unique'=>"cette adresse e-mail est deja utilisée",
            'nom_professionnel.max'=>"le nom ne peut pas depasser 50 caracteres",
            'prenom_professionnel.max'=>"le prenom TVA ne peut pas depasser 50 caracteres",
            'adresse_professionnel.max'=>"l'adresse TVA ne peut pas depasser 50 caracteres",
            'code_postal_professionnel.regex'=>"le code postal n'est pas valide",
            'pays_professionnel.max'=>"le pays ne peut pas depasser 50 caracteres",
            'ville_professionnel.max'=>"la ville ne peut pas depasser 50 caracteres",
            'telephone_professionnel.regex'=> "le numero de telephone n'est pas valide",

        ]);



        session([
            'registerPro' => $request->only(
                'nom_societe',
                'id_user_connecte',
                'numero_TVA',
                'activite_professionnel',
                'email_professionnel',
                'nom_professionnel',
                'prenom_professionnel',
                'adresse_professionnel',
                'code_postal_professionnel',
                'pays_professionnel',
                'ville_professionnel',
            ) + ['telephone_professionnel' => $fullPhone]
        ]);
        
        return redirect()->route('registerPro.step2');
        
    }
    public function step2()
    {
            
        if (!session()->has('registerPro')) {
            return redirect()->route('registerPro.step1');
        }


        return view('pro_account_creation_2');
    }
        public function step2Post(Request $request)
        {
            
             $this->current_user =Auth::user();
            
            try
            {
            $request->validate([
                'password_professionnel' => 'required|min:12|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                
                
            ],['password_professionnel.min'=>"Le mot de passe doit contenir au moins 12 caracteres",
            'password_professionnel.regex'=> "le mot de passe doit contenir une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial",
            'password_professionnel.confirmed'=>"Les mots de passe ne correspondent pas"]);
            }   
            catch(exception )
            {
                dd(ex);
            }

            $data = session('registerPro');
            if (!$data) {
                
                return redirect()->route('registerPro.step1')
                                ->with('error', 'Veuillez remplir la première étape du formulaire.');
            }
        
            $user = Pro::create([
                'nom_societe' => $data['nom_societe'],
                'numero_tva' => $data['numero_TVA'],
                'id_user_connecte'=>$this->current_user->id_user_connecte,
                'prenom_user_connecte' => $this->current_user->prenom_user_connecte,
                'courriel_user_connecte' => $this->current_user->courriel_user_connecte,
                'surnom_user_connecte' => $this->current_user->surnom_user_connecte,
                'date_de_naissance_user_connecte' => $this->current_user->date_de_naissance_user_connecte,
                'pays_de_naissance_user_connecte' => $this->current_user->pays_de_naissance_user_connecte,
                'favori_user_connecte' => $this->current_user->favori_user_connecte,
                'langue_user_connecte' => $this->current_user->langue_user_connecte,
                'activite_professionnel' => $data['activite_professionnel'],
                'email_professionnel' => $data['email_professionnel'],
                'nom_professionnel' => $data['nom_professionnel'],
                'prenom_professionnel' => $data['prenom_professionnel'],
                'adresse_professionnel' => $data['adresse_professionnel'],
                'code_postal_professionnel' => $data['code_postal_professionnel'],
                'pays_professionnel' => $data['pays_professionnel'],
                'ville_professionnel' => $data['ville_professionnel'],
                'telephone_professionnel' => $data['telephone_professionnel'],
                'password_professionnel' => Hash::make($request->password_professionnel),
                
            ]);
           
            session()->forget('registerPro');
           
            return redirect('/')->with('success', 'Votre compte professionnel a été créé!');
            
        }
 

    }
