<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
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
}