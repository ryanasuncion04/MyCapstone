<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FarmProduce;

class DashboardController extends Controller
{
    public function index()
    {
        // Load ALL farm produces system-wide
       // $produces = FarmProduce::with('farmer')->get();

        $produces = FarmProduce::with('farmer')
            ->where('status', 'available')
            ->whereColumn('quantity', '>', 'reserved_quantity')
            ->get();
      //  dd($produces);

        return view('customer.products.index', compact('produces'));
    }
}
