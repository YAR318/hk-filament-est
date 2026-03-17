<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    /**
     * Enviar OTP al correo
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'No encontramos una cuenta con este correo.'
        ]);

        $email = $request->email;
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar OTP en cache por 10 minutos
        Cache::put("otp_{$email}", $otp, now()->addMinutes(10));

        // Enviar correo con OTP (usará log driver en desarrollo)
        Mail::raw("Tu código de verificación es: {$otp}\n\nEste código expira en 10 minutos.", function ($message) use ($email) {
            $message->to($email)
                ->subject('Código de Verificación - Hunabku');
        });

        // En entorno local, guardar OTP en flash para mostrarlo en pantalla
        if (app()->environment('local')) {
            session()->flash('dev_otp', $otp);
        }

        return redirect()->route('otp.verify.form', ['email' => $email])
            ->with('success', 'Código enviado a tu correo.');
    }

    /**
     * Mostrar formulario para verificar OTP
     */
    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-otp', [
            'email' => $request->email
        ]);
    }

    /**
     * Verificar OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $email = $request->email;
        $inputOtp = $request->otp;
        $cachedOtp = Cache::get("otp_{$email}");

        if (!$cachedOtp || $cachedOtp !== $inputOtp) {
            return back()->withErrors(['otp' => 'Código inválido o expirado.'])->withInput();
        }

        // OTP válido - limpiar cache y loguear
        Cache::forget("otp_{$email}");

        $user = User::where('email', $email)->first();
        Auth::login($user);
        session()->regenerate(); // Invalida sesiones anteriores

        // Redirigir según rol
        if ($user->can('acceder_panel')) {
            return redirect('/admin');
        }

        // Redirigir perfil al otro sistema
        return redirect()->away(env('AUTH_SERVER_URL', 'http://localhost:8001') . '/profile');
    }

    /**
     * Mostrar formulario para cambiar contraseña
     */
    public function showPasswordForm(Request $request)
    {
        $email = $request->email;
        $token = base64_decode($request->token);
        $cachedToken = Cache::get("password_reset_{$email}");

        if (!$cachedToken || $cachedToken !== $token) {
            return redirect()->route('login')->withErrors(['error' => 'Enlace inválido o expirado.']);
        }

        return view('auth.reset-password-otp', [
            'email' => $email,
            'token' => $request->token
        ]);
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|min:8|confirmed'
        ]);

        $email = $request->email;
        $token = base64_decode($request->token);
        $cachedToken = Cache::get("password_reset_{$email}");

        if (!$cachedToken || $cachedToken !== $token) {
            return redirect()->route('login')->withErrors(['error' => 'Enlace inválido o expirado.']);
        }

        // Actualizar contraseña
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Limpiar cache
        Cache::forget("password_reset_{$email}");

        // Loguear al usuario
        Auth::login($user);
        session()->regenerate(); // Invalida sesiones anteriores

        // Redirigir según rol
        if ($user->can('acceder_panel')) {
            return redirect('/admin');
        }

        return redirect()->away(env('AUTH_SERVER_URL', 'http://localhost:8001') . '/profile');
    }
}