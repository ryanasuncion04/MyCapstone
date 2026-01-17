<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

// app/Http/Controllers/FarmerController.php

class FarmerController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $farmers = Farmer::where('municipality', $user->municipality)
            ->latest()
            ->get();

        return view('manager.farmers.index', compact('farmers'));
    }

    public function create()
    {
        return view('manager.farmers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'municipality' => 'required|string|max:255',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('farmers', 'public');
        }

        Farmer::create($data);

        return redirect()
            ->route('manager.farmers.index')
            ->with('success', 'Farmer created successfully.');
    }


    public function edit(Farmer $farmer)
    {
        return view('manager.farmers.edit', compact('farmer'));
    }


    public function update(Request $request, Farmer $farmer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:50',
            'municipality' => 'required|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image replacement
        if ($request->hasFile('image')) {

            // Delete old image if exists
            if ($farmer->image) {
                Storage::disk('public')->delete($farmer->image);
            }

            $validated['image'] = $request
                ->file('image')
                ->store('farmers', 'public');
        }

        // Update farmer
        $farmer->update($validated);

        return redirect()
            ->route('manager.farmers.index')
            ->with('success', 'Farmer updated successfully.');
    }


    public function destroy(Farmer $farmer)
    {
        // Delete image if it exists
        if ($farmer->image) {
            Storage::disk('public')->delete($farmer->image);
        }

        // Delete farmer (farm_produces will cascade if FK is set)
        $farmer->delete();

        return redirect()
            ->route('manager.farmers.index')
            ->with('success', 'Farmer deleted successfully.');
    }
}

