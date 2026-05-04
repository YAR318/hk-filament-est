<?php

namespace App\Filament\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        // Usuarios con acceso al panel admin
        if ($user->can('acceder_panel')) {
            return redirect()->intended('/admin');
        }

        // Usuarios comunes van a la pantalla de bienvenida
        return redirect('/');
    }
}