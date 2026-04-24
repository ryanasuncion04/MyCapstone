<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FarmProduce;
use Carbon\Carbon;
use App\Models\Farmer;

class DashboardController extends Controller
{
    public function index()
    {
        // Products for filter dropdown
        $products = FarmProduce::query()
            ->where('status', 'available')
            ->distinct()
            ->pluck('product');
        // dd($products);

        return view('admin.dashboard', compact('products'));
    }

    public function produceMap(Request $request)
    {
        $product = $request->get('product');
        $limit = $request->get('limit', 3); // 👈 default = 3

        $query = FarmProduce::query()
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->where('farm_produces.status', 'available');

        if ($product) {
            $query->where('farm_produces.product', $product);
        }

        $data = $query
            ->groupBy('farmers.municipality')
            ->select(
                'farmers.municipality',
                DB::raw('SUM(farm_produces.quantity) as total_quantity')
            )
            ->get();

        return response()->json([
            'data' => $data,
            'top' => $data->sortByDesc('total_quantity')->take($limit)->values(), // 👈 dynamic
        ]);
    }
    public function analytics()
    {
        $products = FarmProduce::distinct()->pluck('product');
        $municipalities = Farmer::distinct()->pluck('municipality');

        return view('admin.produce.analytics', compact('products', 'municipalities'));
    }

    public function data(Request $request)
    {
        $product = $request->get('product');
        $municipality = $request->get('municipality');
        $range = $request->get('range', 'monthly');

        $dateFormat = match ($range) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            default => '%Y-%m',
        };

        /*
        |------------------------------------------------------------------
        | 1. PRODUCE TRENDS
        |------------------------------------------------------------------
        */
        $trends = FarmProduce::query()
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->where('farm_produces.status', 'available')
            ->when($product, fn($q) => $q->where('farm_produces.product', $product))
            ->when($municipality, fn($q) => $q->where('farmers.municipality', $municipality))
            ->select(
                DB::raw("DATE_FORMAT(farm_produces.created_at, '{$dateFormat}') as period"),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        /*
        |------------------------------------------------------------------
        | 2. PRODUCT DISTRIBUTION
        |------------------------------------------------------------------
        */
        $productDistribution = FarmProduce::query()
            ->where('status', 'available')
            ->select('product', DB::raw('SUM(quantity) as total'))
            ->groupBy('product')
            ->get();

        /*
        |------------------------------------------------------------------
        | 3. AVERAGE PRICE PER MUNICIPALITY
        |------------------------------------------------------------------
        */
        $avgPrice = FarmProduce::query()
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->select(
                'farmers.municipality',
                DB::raw('AVG(price) as avg_price')
            )
            ->groupBy('farmers.municipality')
            ->get();

        /*
        |------------------------------------------------------------------
        | 4. TOP FARMERS BY YIELD
        |------------------------------------------------------------------
        */
        $yieldPerFarmer = FarmProduce::query()
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->select(
                'farmers.name',
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('farmers.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return response()->json([
            'trends' => $trends,
            'productDistribution' => $productDistribution,
            'avgPrice' => $avgPrice,
            'yieldPerFarmer' => $yieldPerFarmer,
        ]);
    }


    public function visualization()
    {
        $products = FarmProduce::where('status', 'available')
            ->distinct()
            ->pluck('product');

        return view('admin.produce.visualization', compact('products'));
    }

    /**
     * Choropleth + Top 3
     */
    public function visualizationData(Request $request)
    {
        $product = $request->get('product');

        $query = FarmProduce::query()
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->where('farm_produces.status', 'available');

        if ($product) {
            $query->where('farm_produces.product', $product);
        }

        $data = $query
            ->groupBy('farmers.municipality')
            ->select(
                'farmers.municipality',
                DB::raw('SUM(farm_produces.quantity) as total_quantity')
            )
            ->get();

        return response()->json([
            'data' => $data,
            'top3' => $data->sortByDesc('total_quantity')->take(3)->values(),
        ]);
    }

    /**
     * Municipality trend (monthly)
     */
    public function municipalityTrend(string $municipality)
    {
        $trend = FarmProduce::query()
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->where('farmers.municipality', $municipality)
            ->where('farm_produces.status', 'available')
            ->groupBy(DB::raw('DATE_FORMAT(farm_produces.created_at, "%Y-%m")'))
            ->select(
                DB::raw('DATE_FORMAT(farm_produces.created_at, "%Y-%m") as period'),
                DB::raw('SUM(farm_produces.quantity) as total_quantity')
            )
            ->orderBy('period')
            ->get();

        return response()->json($trend);
    }

    //municipality perfomance view
    public function municipalityMap()
    {
        return view('admin.produce.municipality');
    }


    public function municipalityData($municipality)
    {
        $municipalityLower = strtolower($municipality);
        $product = request('product');

        /*
        |----------------------------------------------------------
        | SUMMARY
        |----------------------------------------------------------
        */
        $summary = DB::table('farm_produces')
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->where('farm_produces.status', 'available')
            ->whereRaw('LOWER(farmers.municipality) = ?', [$municipalityLower])
            ->when($product, fn($q) => $q->where('farm_produces.product', $product))
            ->selectRaw('
            COALESCE(SUM(quantity), 0) as total_quantity,
            COALESCE(AVG(price), 0) as avg_price,
            COUNT(DISTINCT farmers.id) as farmer_count
        ')
            ->first();

        /*
        |----------------------------------------------------------
        | PRODUCTS
        |----------------------------------------------------------
        */
        $products = DB::table('farm_produces')
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->where('farm_produces.status', 'available')
            ->whereRaw('LOWER(farmers.municipality) = ?', [$municipalityLower])
            ->when($product, fn($q) => $q->where('farm_produces.product', $product))
            ->groupBy('product')
            ->select(
                'product',
                DB::raw('AVG(price) as avg_price'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->orderByDesc('total_quantity')
            ->get();

        /*
        |----------------------------------------------------------
        | TOP FARMERS PER PRODUCT
        |----------------------------------------------------------
        */
        $topFarmersPerProduct = DB::table('farm_produces as fp')
            ->join('farmers as f', 'fp.farmer_id', '=', 'f.id')
            ->where('fp.status', 'available')
            ->whereRaw('LOWER(f.municipality) = ?', [$municipalityLower])
            ->when($product, fn($q) => $q->where('fp.product', $product))
            ->select(
                'fp.product',
                'f.name',
                DB::raw('SUM(fp.quantity) as total_quantity')
            )
            ->groupBy('fp.product', 'f.name')
            ->orderByDesc('total_quantity')
            ->get()
            ->groupBy('product')
            ->map(fn($group) => $group->first())
            ->values();

        return response()->json([
            'summary' => $summary,
            'products' => $products,
            'topFarmersPerProduct' => $topFarmersPerProduct,
        ]);
    }

}
