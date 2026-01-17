<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\RegisterViewResponse as RegisterViewResponseContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterViewResponse implements RegisterViewResponseContract
{
    public function toResponse($request): Response
    {
        return response()->view('livewire.auth.register');
    }
}