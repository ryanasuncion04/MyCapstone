<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ratings;
use App\Models\Preorder;

class RatingController extends Controller
{
    public function store(Request $request, Preorder $preorder)
    {
        if ($preorder->customer_id !== auth()->id()) {
            abort(403);
        }

        if ($preorder->status !== 'approved') {
            return back()->withErrors('You can only rate completed orders.');
        }

        if ($preorder->rating) {
            return back()->withErrors('You already rated this order.');
        }

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ]);

        Ratings::create([
            'customer_id' => auth()->id(),
            'farmer_id' => $preorder->produce->farmer->id, // ⭐ KEY CHANGE
            'preorder_id' => $preorder->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Rating submitted successfully.');
    }
}
