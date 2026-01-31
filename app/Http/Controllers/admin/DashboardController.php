<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FarmProduce;

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

     public function analytics()
    {
        // For dropdown filters
        $products = FarmProduce::distinct()->pluck('product');

        return view('admin.produce.analytics', compact('products'));
    }

    public function data(Request $request)
    {
        $product = $request->get('product');
        $range = $request->get('range', 'monthly'); // daily | weekly | monthly

        /*
        |--------------------------------------------------------------------------
        | 1. PRODUCE TRENDS OVER TIME
        |--------------------------------------------------------------------------
        */
        $dateFormat = match ($range) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            default => '%Y-%m',
        };

        $trendQuery = FarmProduce::query()
            ->where('status', 'available')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                DB::raw('SUM(quantity) as total_quantity')
            );

        if ($product) {
            $trendQuery->where('product', $product);
        }

        $trends = $trendQuery
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 2. MONTHLY / WEEKLY COMPARISON BY YEAR
        |--------------------------------------------------------------------------
        */
        $comparison = FarmProduce::query()
            ->where('status', 'available')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->when($product, fn ($q) => $q->where('product', $product))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3. AVERAGE PRICE PER PRODUCT PER MUNICIPALITY
        |--------------------------------------------------------------------------
        */
        $avgPrice = FarmProduce::query()
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->select(
                'farm_produces.product',
                'farmers.municipality',
                DB::raw('AVG(price) as avg_price')
            )
            ->groupBy('farm_produces.product', 'farmers.municipality')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4. YIELD PER FARMER (RANKING)
        |--------------------------------------------------------------------------
        */
        $yieldPerFarmer = FarmProduce::query()
            ->join('farmers', 'farm_produces.farmer_id', '=', 'farmers.id')
            ->select(
                'farmers.name',
                DB::raw('SUM(farm_produces.quantity) as total_quantity')
            )
            ->groupBy('farmers.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return response()->json([
            'trends' => $trends,
            'comparison' => $comparison,
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
}
