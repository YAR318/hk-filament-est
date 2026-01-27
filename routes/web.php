<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;

// Login principal estilo Canva
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin');
    }
    return view('auth.login-canva');
})->name('login');

// Login con email (redirige a Filament)
Route::get('/login/email', function () {
    return view('auth.email-login');
})->name('login.email');

// OAuth Routes
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
    ->name('auth.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
    ->name('auth.callback');
