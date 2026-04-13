<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\ProfileController;

// Login principal estilo Canva
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        // Redirigir según el rol
        if (in_array($user->role, ['admin', 'supervisor', 'operador', 'super_admin'])) {
            return redirect('/admin');
        }

        // Usuarios comunes ven la pantalla intermedia
        return view('auth.interstitial');
    }
    return view('auth.login-canva');
})->name('login');

// Perfil para usuarios comunes (Redirección externa)
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
            return view('profile', ['user' => Auth::user()]);
        }
        )->name('profile');
        Route::post('/profile/logout', [ProfileController::class , 'logout'])->name('profile.logout');
    });

// Login con email (redirige a Filament)
Route::get('/login/email', function () {
    return view('auth.email-login');
})->name('login.email');

// OAuth Routes
Route::get('/auth/{provider}', [SocialAuthController::class , 'redirectToProvider'])
    ->name('auth.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class , 'handleProviderCallback'])
    ->name('auth.callback');

// OTP Routes
Route::post('/otp/send', [OtpController::class , 'sendOtp'])->name('otp.send');
Route::get('/otp/verify', [OtpController::class , 'showVerifyForm'])->name('otp.verify.form');
Route::post('/otp/verify', [OtpController::class , 'verifyOtp'])->name('otp.verify');
Route::get('/otp/password', [OtpController::class , 'showPasswordForm'])->name('otp.password.form');
Route::post('/otp/password', [OtpController::class , 'updatePassword'])->name('otp.password.update');