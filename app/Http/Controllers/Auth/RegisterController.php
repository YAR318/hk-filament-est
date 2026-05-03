<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function showForm()
    {
        return view('auth.register');
    }

    /**
     * Registrar nuevo usuario
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresa un correo válido.',
            'email.unique' => 'Ya existe una cuenta con este correo.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Crear usuario sin verificar
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // Generar token de verificación de 6 dígitos
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar token en cache por 30 minutos
        Cache::put("email_verify_{$user->email}", $token, now()->addMinutes(30));

        // Enviar correo con token (se envía vía Resend)
        Mail::send('emails.verification-token', ['token' => $token, 'name' => $user->name], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verifica tu cuenta - Hunabku');
        });

        return redirect()->route('verify.email.form', ['email' => $user->email])
            ->with('success', 'Te hemos enviado un código de verificación a tu correo.');
    }

    /**
     * Mostrar formulario de verificación de email
     */
    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-email', [
            'email' => $request->email,
        ]);
    }

    /**
     * Verificar token de email
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6',
        ]);

        $email = $request->email;
        $inputToken = $request->token;
        $cachedToken = Cache::get("email_verify_{$email}");

        if (!$cachedToken || $cachedToken !== $inputToken) {
            return back()->withErrors(['token' => 'Código inválido o expirado.'])->withInput();
        }

        // Token válido - verificar la cuenta
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['token' => 'No se encontró la cuenta.'])->withInput();
        }

        $user->email_verified_at = now();
        $user->save();

        // Limpiar cache
        Cache::forget("email_verify_{$email}");

        // Loguear al usuario
        Auth::login($user);
        session()->regenerate();

        // Redirigir según rol
        if ($user->can('acceder_panel')) {
            return redirect('/admin');
        }

        return redirect('/');
    }

    /**
     * Reenviar token de verificación
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        // Verificar que el usuario no esté ya verificado
        if ($user->email_verified_at) {
            return redirect()->route('login')
                ->with('success', 'Tu cuenta ya está verificada. Puedes iniciar sesión.');
        }

        // Generar nuevo token
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put("email_verify_{$email}", $token, now()->addMinutes(30));

        // Enviar correo
        Mail::send('emails.verification-token', ['token' => $token, 'name' => $user->name], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verifica tu cuenta - Hunabku');
        });

        return back()->with('success', 'Código reenviado a tu correo.');
    }
}
