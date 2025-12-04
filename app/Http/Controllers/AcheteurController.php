<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcheteurController extends Controller
{
    public function index(){
    	return view ("acheteur-list", ['acheteurs'=>Acheteur::all() ]);
    }
}
