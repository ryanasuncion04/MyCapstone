<?php

namespace App\Http\Controllers;

use App\Models\FarmProduce;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
// app/Http/Controllers/FarmProduceController.php

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
        return view('manager.farm-produce.create');
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
            'status' => ['nullable', 'in:draft,available,unavailable'],
        ]);

        /* =========================
         | Handle Image Upload
         ========================= */
        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('farm-produces', 'public');
        }

        /* =========================
         | Default System Values
         ========================= */
        $validated['reserved_quantity'] = 0;
        $validated['status'] = $validated['status'] ?? 'draft';

        /* =========================
         | Create Produce
         ========================= */
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
        return view('manager.farm-produce.edit', compact('farmProduce'));
    }

    public function update(Request $request, FarmProduce $farmProduce)
    {
        $validated = $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'product' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        // Handle image update
        if ($request->hasFile('image')) {

            // Delete old image if exists
            if ($farmProduce->image) {
                Storage::disk('public')->delete($farmProduce->image);
            }

            // Store new image
            $validated['image'] = $request
                ->file('image')
                ->store('farm-produces', 'public');
        }

        // Update produce record
        $farmProduce->update($validated);

        return redirect()
            ->route('manager.farm-produce.index')
            ->with('success', 'Farm produce updated successfully.');
    }

    public function destroy(FarmProduce $farmProduce)
    {
        // Delete image from storage if it exists
        if ($farmProduce->image) {
            Storage::disk('public')->delete($farmProduce->image);
        }

        // Delete the database record
        $farmProduce->delete();

        return redirect()
            ->route('manager.farm-produce.index')
            ->with('success', 'Farm produce deleted successfully.');
    }
}

