<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nation;

class NationController extends Controller
{
    public function index(){
    	return view ("nation-list", ['nations'=>Nation::all() ]);
    }
}
