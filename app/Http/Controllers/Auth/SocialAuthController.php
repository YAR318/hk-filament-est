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
                ]);
            }
            
            // Login user
            Auth::login($user, true);
            
            // Redirect to Filament dashboard
            return redirect()->intended('/admin');
            
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Error al autenticar con ' . ucfirst($provider));
        }
    }
}
