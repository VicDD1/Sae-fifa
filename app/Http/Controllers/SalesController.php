<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\reglement;

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
public function localisation()
{
    $salesByLocation = reglement::query()
        ->join('commande', 'reglement.id_commande', '=', 'commande.id_commande')
        ->join('adresse', 'commande.id_adresse', '=', 'adresse.id_adresse')
        ->whereNotNull('adresse.latitude')
        ->whereNotNull('adresse.longitude')
        ->selectRaw('
            adresse.latitude,
            adresse.longitude,
            SUM(reglement.montant_regle) as total_sales
        ')
        ->groupBy('adresse.latitude', 'adresse.longitude')
        ->get();

    return response()->json($salesByLocation);
}
}