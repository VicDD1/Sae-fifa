<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Taille;

class TailleControler extends Controller
{
    public function index(){
    	return view ("taille-list", ['tailles'=>Taille::all() ]);
    }
}
