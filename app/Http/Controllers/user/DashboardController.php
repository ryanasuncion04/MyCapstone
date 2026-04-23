<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FarmProduce;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $produces = FarmProduce::with([
            'farmer.ratings.customer'
        ])
            ->where('status', 'available')
            ->whereColumn('quantity', '>', 'reserved_quantity')
            ->get();

        // ✅ attach computed average rating to farmer
        $produces->each(function ($produce) {
            $produce->farmer->average_rating =
                $produce->farmer->ratings->avg('rating') ?? 0;
        });

        $products = FarmProduce::query()
            ->where('status', 'available')
            ->whereColumn('quantity', '>', 'reserved_quantity')
            ->distinct()
            ->pluck('product');

        return view('customer.products.index', compact('produces', 'products'));
    }

    public function map()
    {
        // Available produce list for filter
        $products = FarmProduce::query()
            ->where('status', 'available')
            ->distinct()
            ->pluck('product');
        // dd($products);

        return view('customer.products.mapProduce', compact('products'));
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
}
