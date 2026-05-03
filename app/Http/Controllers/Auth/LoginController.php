<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Iniciar sesión con correo y contraseña.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->can('acceder_panel')) {
                return redirect('/admin');
            }

            return redirect('/');
        }

        return back()->with('login_error', 'Las credenciales proporcionadas son incorrectas.')->withInput($request->only('email'));
    }
}
