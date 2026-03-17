<?php

namespace App\Filament\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = Auth::user();

        // Usuarios con acceso al panel admin
        if ($user->can('acceder_panel')) {
            return redirect()->intended('/admin');
        }

        // Usuarios comunes van a su perfil
        return redirect('/profile');
    }
}