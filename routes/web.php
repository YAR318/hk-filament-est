<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ProfileController;

// Login principal estilo Canva
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        // Redirigir según el rol
        if (in_array($user->role, ['admin', 'supervisor', 'operador', 'super_admin'])) {
            return redirect('/admin');
        }

        // Usuarios comunes van a su perfil
        return redirect('/profile');
    }
    return view('auth.login-canva');
})->name('login');

// Perfil para usuarios comunes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/logout', [ProfileController::class, 'logout'])->name('profile.logout');
});

// Login con email (redirige a Filament)
Route::get('/login/email', function () {
    return view('auth.email-login');
})->name('login.email');

// OAuth Routes
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
    ->name('auth.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
    ->name('auth.callback');
