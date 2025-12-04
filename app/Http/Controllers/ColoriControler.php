<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Colori;

class ColoriControler extends Controller
{
    public function index(){
    	return view ("colori-list", ['coloris'=>Colori::all() ]);
    }
}
