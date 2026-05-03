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
                // Correos que automáticamente obtienen rol admin
                $adminEmails = [
                    'agenteia.hunabku@gmail.com',
                ];

                $isAdmin = in_array($socialUser->getEmail(), $adminEmails);

                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'role' => $isAdmin ? 'admin' : 'user',
                ]);

                // Asignar rol Spatie automáticamente
                if ($isAdmin) {
                    $user->assignRole('admin');
                }
            } elseif (!$user->email_verified_at) {
                // Si el usuario existe pero no ha verificado su correo, lo verificamos ahora ya que entró con Google
                $user->email_verified_at = now();
                $user->save();
            }

            // Login user
            Auth::login($user, true);
            session()->regenerate(); // Invalida sesiones anteriores

            // Redirigir según el rol del usuario
            return $this->redirectByRole($user);

        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SocialAuth Error: ' . $e->getMessage(), [
                'provider' => $provider,
                'exception' => get_class($e),
            ]);
            return redirect('/')
                ->with('error', 'Error al autenticar con ' . ucfirst($provider));
        }
    }

    /**
     * Redirigir según el rol del usuario
     */
    protected function redirectByRole(User $user): \Illuminate\Http\RedirectResponse
    {
        // Usuarios con acceso al panel admin
        if ($user->can('acceder_panel')) {
            return redirect('/admin');
        }

        // Usuarios comunes van a la pantalla de bienvenida
        return redirect('/');
    }
}