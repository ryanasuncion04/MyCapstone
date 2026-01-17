<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\LoginViewResponse;
use App\Actions\Fortify\LoginResponse;
use App\Actions\Fortify\RegisterViewResponse;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginViewResponse as LoginViewResponseContract;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterViewResponse as RegisterViewResponseContract;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginViewResponseContract::class, LoginViewResponse::class);
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(RegisterViewResponseContract::class, RegisterViewResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // Custom authentication with role-based redirect
        // Fortify::authenticateUsing(function (Request $request) {
        //     $user = User::where('email', $request->email)->first();

        //     if ($user && Hash::check($request->password, $user->password)) {
        //         return $user;
        //     }

        //     return null;
        // });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return null;
            }

            if (!Hash::check($request->password, $user->password)) {
                return null;
            }

            if (
                $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
                !$user->hasVerifiedEmail()
            ) {
                return null;
            }

            return $user;
        });


        // Redirect based on user role after login
        Fortify::redirects('login', function (Request $request) {
            $user = auth()->user();

            return match ($user->role) {
                UserRole::Admin => route('admin.dashboard'),
                UserRole::Manager => route('manager.dashboard'),
                default => route('dashboard'),
            };
        });
    }
}
