<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User_connecte;

class UserControler extends Controller
{
    public function index(){
    	return view ("user-list", ['users'=>User_connecte::all() ]);
    }
}
