<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function index()
    {
        $monthlySales = DB::table('reglement')
        ->select(
            DB::raw('EXTRACT(YEAR FROM date_reglement) AS year'),
            DB::raw('EXTRACT(MONTH FROM date_reglement) AS month'),
            DB::raw('SUM(montant_regle) AS total_sales')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();
        return view('statistique', compact('monthlySales'));
    }
}