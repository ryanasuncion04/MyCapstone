<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();

        $geojson = json_decode(
            File::get(public_path('maps/ilocos-norte.geojson')),
            true
        );

        $municipalities = collect($geojson['features'])
            ->map(fn($feature) => data_get($feature, 'properties.Municipality'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('admin.users.index', compact('users', 'municipalities'));
    }

    public function updateRole(Request $request, User $user)
    {
        // prevent changing your own role
        if ($user->id === auth()->id()) {
            abort(403);
        }
       
        $validated = $request->validate([
            'role' => ['required', 'in:admin,manager,user'],
        ]);

        $user->update([
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'User role updated successfully.');
    }

    public function updateMunicipality(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'municipality' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'municipality' => $validated['municipality'],
        ]);

        return back()->with('success', 'Municipality updated.');
    }
}