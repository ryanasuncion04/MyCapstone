<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();
        $role = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        return match($role) {
            UserRole::Admin->value => redirect()->route('admin.dashboard'),
            UserRole::Manager->value => redirect()->route('manager.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}

