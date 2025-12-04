<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 use App\Models\Produit_Propose as Product;
 use App\Models\User_connecte as User;
 use App\Models\Professionnel as Pro;
class ProduitProposeController extends Controller
{
    
    public function step1()
    {
        return view('product_demand');
        
    }

    
    public function step1Post(Request $request)
    {
        $this->current_user =Auth::user();
       
        $request->validate([
            'nom_produit_propose' => 'required',
            'description_produit_propose' => 'required',

        ],[
            'nom_produit_propose.required' => "ce champs ne peut pas etre vide",
            'description_produit_propose.required' => "ce champs ne peut pas etre vide",
           
        ]);

        session([
            'registerProduct' => $request->only(
                'nom_produit_propose',
                'description_produit_propose',

            )
        ]);
        $data = session('registerProduct');
       Product::create([
            'id_professionnel' => $this->current_user->professionnel->id_professionnel,
            'id_user_connecte'=> $this->current_user->id_user_connecte,
            'description' => $data['description_produit_propose'],
            'nom_demande' => $data['nom_produit_propose'],
            
        ]);

        return redirect('/')->with('success', 'Votre demande de produit a été créée est sera examinée dans les 30 jours !');
    }
    
}





