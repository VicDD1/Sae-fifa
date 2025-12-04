<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colori;

class CompetitionControler extends Controller
{
    public function index(){
    	return view ("competition-list", ['competitions'=>Competition::all() ]);
    }
}
