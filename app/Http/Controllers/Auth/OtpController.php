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
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Si no existe, simulamos éxito para no revelar si la cuenta existe o no
            return redirect()->route('otp.verify.form', ['email' => $email])
                ->with('success', 'Si hay una cuenta asociada a este correo, te hemos enviado un código.');
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar OTP en cache por 10 minutos
        Cache::put("otp_{$email}", $otp, now()->addMinutes(10));

        // Enviar correo con OTP (se envía vía Resend)
        Mail::raw("Tu código de verificación es: {$otp}\n\nEste código expira en 10 minutos.", function ($message) use ($email) {
            $message->to($email)
                ->subject('Código de Verificación - Hunabku');
        });

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

        // Usuarios comunes van a la pantalla de bienvenida o dashboard
        return redirect('/');
    }

    /**
     * Mostrar formulario para recuperar contraseña
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Enviar token de recuperación de contraseña
     */
    public function sendPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            // No revelar si existe o no
            return redirect()->route('password.reset.form', ['email' => $email])
                ->with('success', 'Si hay una cuenta asociada a este correo, te enviamos un código de recuperación.');
        }

        // Bloquear recuperación para usuarios con acceso al panel
        if ($user->can('acceder_panel')) {
            return back()->withErrors([
                'email' => 'Las cuentas con acceso administrativo no pueden cambiar su contraseña por este medio. Contacta a un administrador.'
            ])->withInput();
        }

        // Generar token de 6 dígitos
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put("password_reset_{$email}", $token, now()->addMinutes(30));

        // Enviar correo con token (vía Resend)
        Mail::send('emails.password-reset-token', ['token' => $token, 'name' => $user->name], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Recuperar Contraseña - Hunabku');
        });

        return redirect()->route('password.reset.form', ['email' => $email])
            ->with('success', 'Te hemos enviado un código de recuperación a tu correo.');
    }

    /**
     * Mostrar formulario para ingresar token y nueva contraseña
     */
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', [
            'email' => $request->email,
        ]);
    }

    /**
     * Verificar token y actualizar contraseña
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed'
        ], [
            'token.size' => 'El código debe ser de 6 dígitos.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $email = $request->email;
        $inputToken = $request->token;
        $cachedToken = Cache::get("password_reset_{$email}");

        if (!$cachedToken || $cachedToken !== $inputToken) {
            return back()->withErrors(['token' => 'Código inválido o expirado.'])->withInput();
        }

        $user = User::where('email', $email)->first();

        // Doble verificación: bloquear usuarios con acceso al panel
        if ($user->can('acceder_panel')) {
            return back()->withErrors([
                'token' => 'Las cuentas con acceso administrativo no pueden cambiar su contraseña por este medio.'
            ]);
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        // Limpiar cache
        Cache::forget("password_reset_{$email}");

        // Loguear al usuario
        Auth::login($user);
        session()->regenerate();

        return redirect('/');
    }
}