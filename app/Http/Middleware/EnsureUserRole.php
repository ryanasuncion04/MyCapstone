<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role->value;

        if (!in_array($userRole, $roles)) {
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}