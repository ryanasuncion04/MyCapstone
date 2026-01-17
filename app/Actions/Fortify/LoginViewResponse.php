<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginViewResponse as LoginViewResponseContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginViewResponse implements LoginViewResponseContract
{
    public function toResponse($request): Response
    {
        return response()->view('livewire.auth.login');
    }
}