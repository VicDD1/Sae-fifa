<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profetionel;

class ProfetionelControler extends Controller
{
    public function index(){
    	return view ("profetionel-list", ['profetionel'=>Profetionel::all() ]);
    }
}
