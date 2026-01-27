<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to OAuth provider
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle OAuth callback
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Find or create user
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'role' => 'user', // Asignar rol por defecto
                ]);
            }

            // Login user
            Auth::login($user, true);

            // Redirigir según el rol del usuario
            return $this->redirectByRole($user);

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Error al autenticar con ' . ucfirst($provider));
        }
    }

    /**
     * Redirigir según el rol del usuario
     */
    protected function redirectByRole(User $user): \Illuminate\Http\RedirectResponse
    {
        // Usuarios con acceso al panel admin
        if (in_array($user->role, ['admin', 'supervisor', 'operador', 'super_admin'])) {
            return redirect('/admin');
        }

        // Usuarios comunes van a su perfil en el otro sistema
        return redirect()->away('https://hk_autenticacion_est.test/profile');
    }
}
