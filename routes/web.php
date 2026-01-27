<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\HTTP\Controllers\FarmProduceController;
use App\HTTP\Controllers\FarmerController;
use App\Http\Controllers\PreorderController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/products', [UserDashboardController::class, 'index'])
            ->name('products');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
    });
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])
            ->name('dashboard');
    });



use App\Http\Controllers\ChatController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Profile routes
Route::get('/profile', function () {
    return view('profile.edit');
})->name('profile.edit');

Route::put('/profile', function () {
    // Handle profile update
})->name('profile.update');

Route::delete('/profile', function () {
    // Handle profile delete
})->name('profile.destroy');

// Chat routes
Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::get('/chat/mindex', [ChatController::class, 'mindex'])->name('chat.mindex');
Route::get('/chat/adindex', [ChatController::class, 'adindex'])->name('chat.adindex');
Route::post('/chat/start', [ChatController::class, 'startConversation'])->name('chat.start');
Route::post('/chat/mstart', [ChatController::class, 'mstartConversation'])->name('chat.mstart');
Route::post('/chat/adstart', [ChatController::class, 'adstartConversation'])->name('chat.adstart');
Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
Route::get('/mchat/{conversation}', [ChatController::class, 'mshow'])->name('chat.mshow');
Route::get('/adchat/{conversation}', [ChatController::class, 'adshow'])->name('chat.adshow');

// Password edit routes for settings
Route::get('/user/password', function () {
    return view('profile.password');
})->name('user-password.edit');

Route::put('/user/password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'current_password' => ['required'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = auth()->user();

    if (!\Illuminate\Support\Facades\Hash::check($request->input('current_password'), $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    $user->password = \Illuminate\Support\Facades\Hash::make($request->input('password'));
    $user->save();

    return back()->with('status', 'password-updated');
})->name('user-password.update');

// Two-factor placeholder route (Fortify may register its own routes)
Route::get('/two-factor', function () {
    return view('profile.two-factor');
})->name('two-factor.show');

// Appearance settings route
Route::get('/appearance', function () {
    return view('profile.appearance');
})->name('appearance.edit');

Route::post('/appearance', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'theme' => ['required', 'in:light,dark'],
        'primary_color' => ['required'],
        'layout' => ['required', 'in:comfortable,compact'],
    ]);

    \App\Models\Setting::set('theme', $request->input('theme'));
    \App\Models\Setting::set('primary_color', $request->input('primary_color'));
    \App\Models\Setting::set('layout', $request->input('layout'));

    return back()->with('status', 'appearance-updated');
})->name('appearance.update');


//route for farm produce and farmer managament
Route::middleware(['auth'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::resource('farmers', FarmerController::class);
        Route::resource('farm-produce', FarmProduceController::class);
    });

// Manager routes for preorders
Route::middleware(['auth'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        // Preorders management
        Route::get('preorders', [PreorderController::class, 'index'])->name('preorders.index');
        Route::post('preorders/{preorder}/approve', [PreorderController::class, 'approve'])->name('preorders.approve');
        Route::post('preorders/{preorder}/reject', [PreorderController::class, 'reject'])->name('preorders.reject');
    });

// Customer Routes
Route::prefix('customer')->name('customer.')->middleware(['auth'])->group(function () {
    Route::get('preorders', [PreorderController::class, 'customerIndex'])->name('preorders.index');
    Route::delete('preorders/{preorder}/cancel', [PreorderController::class, 'cancel'])->name('preorders.cancel');
    Route::get( 'preorders/create/{produce}',[PreorderController::class, 'create'])->name('preorders.create');
    Route::post('preorders/store/{produce}',[PreorderController::class, 'store'])->name('preorders.store');

});


