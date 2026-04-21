<?php

namespace App\Http\Controllers;

use App\Models\FarmProduce;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class FarmProduceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $produces = FarmProduce::with('farmer')
            ->when($user->municipality, function ($query) use ($user) {
                $query->whereHas('farmer', function ($q) use ($user) {
                    $q->where('municipality', $user->municipality);
                });
            })
            ->latest()
            ->get();

        return view('manager.farm-produce.index', compact('produces'));
    }

    public function create()
    {
        $products = Product::orderBy('product_name')->get();
        return view('manager.farm-produce.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'product' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],

            // STATUS
            'status' => ['nullable', 'in:draft,available,reserved,sold_out,rejected'],

            // AVAILABILITY (NEW)
            'available_from' => ['required', 'date'],
            'available_until' => ['required', 'date', 'after_or_equal:available_from'],
        ]);

        // Attach manager (user)
        $validated['user_id'] = auth()->id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('farm-produces', 'public');
        }

        // Reserved default
        $validated['reserved_quantity'] = 0;

        // Auto status fallback
        $validated['status'] = $validated['status'] ?? 'draft';

        // Create record
        FarmProduce::create($validated);

        return redirect()
            ->route('manager.farm-produce.index')
            ->with('success', 'Farm produce added successfully.');
    }

    public function show(FarmProduce $farmProduce)
    {
        return view('manager.farm-produce.show', compact('farmProduce'));
    }

    public function edit(FarmProduce $farmProduce)
    {
        $products = Product::orderBy('product_name')->get();
        return view('manager.farm-produce.edit', compact('farmProduce', 'products'));
    }

    public function update(Request $request, FarmProduce $farmProduce)
    {
        // Ensure ownership
        if ($farmProduce->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'product' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quantity' => ['required', 'integer', 'min:' . $farmProduce->reserved_quantity],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],

            // STATUS
            'status' => ['required', 'in:draft,available,reserved,sold_out,rejected'],

            // AVAILABILITY (NEW)
            'available_from' => ['required', 'date'],
            'available_until' => ['required', 'date', 'after_or_equal:available_from'],
        ]);

        // Handle new image
        if ($request->hasFile('image')) {
            // delete old image
            if ($farmProduce->image) {
                Storage::disk('public')->delete($farmProduce->image);
            }

            $validated['image'] = $request
                ->file('image')
                ->store('farm-produces', 'public');
        }

        $farmProduce->update($validated);

        return redirect()
            ->route('manager.farm-produce.index')
            ->with('success', 'Farm produce updated successfully.');
    }

    public function destroy(FarmProduce $farmProduce)
    {
        if ($farmProduce->image) {
            Storage::disk('public')->delete($farmProduce->image);
        }

        $farmProduce->delete();

        return redirect()
            ->route('manager.farm-produce.index')
            ->with('success', 'Farm produce deleted successfully.');
    }
}
