<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FarmProduce;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userMunicipality = Auth::user()->municipality;

        $produces = FarmProduce::with('farmer')
            ->whereHas('farmer', function ($q) use ($userMunicipality) {
                $q->where('municipality', $userMunicipality);
            })
            ->get();

        return view('manager.dashboard', compact('produces'));
    }


    public function buyersList(Request $request)
    {
        $query = User::where('role', 'user');

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by municipality
        if ($request->filled('municipality')) {
            $query->where('municipality', $request->municipality);
        }

        $buyers = $query->get();

        // 👇 get unique municipalities for dropdown
        $municipalities = User::where('role', 'user')
            ->select('municipality')
            ->distinct()
            ->pluck('municipality');

        return view('manager.listofbuyers.buyerslist', compact('buyers', 'municipalities'));
    }
}