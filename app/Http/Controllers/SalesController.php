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
                produit.id_categorie as categorie_id,
                SUM(COALESCE(produit.prix_base, 0) * COALESCE(ligne_commande.quantitee, 0)) as total_sales,
                categorie_produit.label_categorie as nom_categorie
            ')
            ->whereNotNull('categorie_produit.label_categorie')  
            ->groupByRaw('year, month, produit.id_categorie, categorie_produit.label_categorie')
            ->orderByRaw('year, month')
            ->get();
    
        $grouped = $monthlySalesByCategory->groupBy('categorie_id');
        
        $catDatasets = [];
        $catColors = [
            'rgba(75,192,192,1)',
            'rgba(255,99,132,1)',
            'rgba(54,162,235,1)',
            'rgba(255,206,86,1)',
            'rgba(153,102,255,1)',
            'rgba(255,159,64,1)',
        ];

        $i = 0;
        $labels = $monthlySalesByCategory
            ->map(fn ($r) => sprintf('%04d-%02d', $r->year, $r->month))
            ->unique()
            ->sort()
            ->values()
            ->toArray();
            
        foreach ($grouped as $catId => $rows) {
            $data = array_fill(0, count($labels), 0);
            $catName = $rows->first()->nom_categorie;
            
            foreach ($rows as $row) {
                $label = sprintf('%04d-%02d', $row->year, $row->month);
                $index = array_search($label, $labels);

                if ($index !== false) {
                    $data[$index] = (float) $row->total_sales;
                }
            }

            $catDatasets[] = [
                'label' => $catName,
                'data' => $data,
                'borderColor' => $catColors[$i % count($catColors)],
                'backgroundColor' => $catColors[$i % count($catColors)],
                'tension' => 0.3,
                'fill' => false,
            ];

            $i++;
        }
    
        // IMPORTANT: Disable the built-in legend for the category chart
        $chartByCategory = Chartjs::build()
            ->name('MonthlySalesByCategory')
            ->type('line')
            ->labels($labels)
            ->datasets($catDatasets)
            ->options([
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Ventes mensuelles par catégorie',
                    ],
                    'legend' => [
                        'display' => false,  // THIS IS THE KEY - DISABLE BUILT-IN LEGEND
                    ],
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true],
                ],
            ]);

        // Return both charts
        return view('statistique', [
            'chart' => $chart,
            'chartByCategory' => $chartByCategory,
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
                'reglement.montant_regle'
            )
            ->get();
    
        return response()->json($sales);
    }
}