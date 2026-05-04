<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisterController;
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

// Login manual (modal)
Route::post('/login/manual', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.manual');

// Global Logout
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// OAuth Routes
Route::get('/auth/{provider}', [SocialAuthController::class , 'redirectToProvider'])
    ->name('auth.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class , 'handleProviderCallback'])
    ->name('auth.callback');

// OTP Routes
Route::post('/otp/send', [OtpController::class , 'sendOtp'])->name('otp.send');
Route::get('/otp/verify', [OtpController::class , 'showVerifyForm'])->name('otp.verify.form');
Route::post('/otp/verify', [OtpController::class , 'verifyOtp'])->name('otp.verify');

// Password Reset Routes
Route::get('/forgot-password', [OtpController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password', [OtpController::class, 'sendPasswordReset'])->name('password.send-reset');
Route::get('/reset-password', [OtpController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [OtpController::class, 'resetPassword'])->name('password.reset');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
Route::get('/verify-email', [RegisterController::class, 'showVerifyForm'])->name('verify.email.form');
Route::post('/verify-email', [RegisterController::class, 'verify'])->name('verify.email');
Route::post('/verify-email/resend', [RegisterController::class, 'resend'])->name('verify.email.resend');