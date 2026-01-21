<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\reglement;
use IcehouseVentures\LaravelChartjs\Facades\Chartjs;

class SalesController extends Controller
{
    public function index()
    {
        // --- 1. Monthly sales (all sales, by year) ---
        $monthlySales = DB::table('reglement')
            ->selectRaw('EXTRACT(YEAR FROM date_reglement) as year, EXTRACT(MONTH FROM date_reglement) as month, SUM(montant_regle) as total_sales')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    
        $months = collect(range(1, 12))
            ->map(fn($m) => Carbon::create()->month($m)->format('M'))
            ->toArray();
    
        $yearsGrouped = $monthlySales->groupBy('year');
        $yearDatasets = [];
        $yearColors = [
            'rgba(75,192,192,1)',
            'rgba(255,99,132,1)',
            'rgba(54,162,235,1)',
            'rgba(255,206,86,1)',
        ];
        $idx = 0;
        foreach ($yearsGrouped as $year => $sales) {
            $data = array_fill(0, 12, 0);
            foreach ($sales as $row) {
                $data[$row->month - 1] = (float) $row->total_sales;
            }
            $yearDatasets[] = [
                'label' => "Ventes $year (€)",
                'data' => $data,
                'borderColor' => $yearColors[$idx % count($yearColors)],
                'backgroundColor' => $yearColors[$idx % count($yearColors)],
                'tension' => 0.2,
                'fill' => false,
            ];
            $idx++;
        }
    
        $chart = Chartjs::build()
            ->name('MonthlySalesByYear')
            ->type('line')
            ->labels($months)
            ->datasets($yearDatasets)
            ->options([
                'responsive' => true,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Ventes mensuelles par année',
                    ],
                ],
                'scales' => ['y' => ['beginAtZero' => true]],
            ]);

        // --- 2. Monthly sales by category ---
        $monthlySalesByCategory = DB::table('commande')
    ->leftJoin('ligne_commande', 'commande.id_commande', '=', 'ligne_commande.id_commande')
    ->leftJoin('produit', 'ligne_commande.id_produit', '=', 'produit.id_produit')
    ->leftJoin('categorie_produit', 'produit.id_categorie', '=', 'categorie_produit.id_categorie')
    ->selectRaw('
        EXTRACT(YEAR FROM commande.date_commande) as year,
        EXTRACT(MONTH FROM commande.date_commande) as month,
        categorie_produit.label_categorie as categorie,
        SUM(COALESCE(produit.prix_base, 0) * COALESCE(ligne_commande.quantitee, 0)) as total
    ')
    ->whereNotNull('categorie_produit.label_categorie')
    ->groupByRaw('year, month, categorie_produit.label_categorie')
    ->orderByRaw('year, month')
    ->get();

    
$years = $monthlySalesByCategory->pluck('year')->unique()->sort()->values();

$labels = collect(range(1, 12))
    ->map(fn ($m) => Carbon::create()->month($m)->format('M'))
    ->toArray();

$chartDataByYear = [];

foreach ($years as $year) {
    $rows = $monthlySalesByCategory->where('year', $year);
    $grouped = $rows->groupBy('categorie');

    $datasets = [];
    $colors = [
        'rgba(75,192,192,1)',
        'rgba(255,99,132,1)',
        'rgba(54,162,235,1)',
        'rgba(255,206,86,1)',
        'rgba(153,102,255,1)',
        'rgba(255,159,64,1)',
    ];

    $i = 0;
    foreach ($grouped as $category => $catRows) {
        $data = array_fill(0, 12, 0);

        foreach ($catRows as $row) {
            $data[$row->month - 1] = (float) $row->total;
        }

        $datasets[] = [
            'label' => $category,
            'data' => $data,
            'borderColor' => $colors[$i % count($colors)],
            'backgroundColor' => $colors[$i % count($colors)],
            'tension' => 0.3,
            'fill' => false,
        ];
        $i++;
    }

    $chartDataByYear[$year] = $datasets;
}
    
        // IMPORTANT: Disable the built-in legend for the category chart
        $initialYear = $years->first();

$chartByCategory = Chartjs::build()
    ->name('MonthlySalesByCategory')
    ->type('line')
    ->labels($labels)
    ->datasets($chartDataByYear[$initialYear])
    ->options([
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => ['display' => false],
            'title' => [
                'display' => true,
                'text' => 'Ventes mensuelles par catégorie',
            ],
        ],
        'scales' => ['y' => ['beginAtZero' => true]],
    ]);

return view('statistique', [
    'chart' => $chart,
    'chartByCategory' => $chartByCategory,
    'categoryDataByYear' => $chartDataByYear,
    'categoryYears' => $years,
]);
    }
    
    public function showSalesMap()
    {
        return view('localisation');
    }

public function getSalesLocalisation()
{
    $sales = DB::table('devfifa.reglement')
        ->join('devfifa.commande', 'reglement.id_commande', '=', 'commande.id_commande')
        ->join('devfifa.adresse', 'commande.id_adresse', '=', 'adresse.id_adresse')
        ->select(
            'adresse.ville_adresse',
            'adresse.latitude',
            'adresse.longitude',
            'reglement.montant_regle',
            DB::raw('EXTRACT(YEAR FROM reglement.date_reglement) as year')
        )
        ->get();

    return response()->json($sales);
}
}