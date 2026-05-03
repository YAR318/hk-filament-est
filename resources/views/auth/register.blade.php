@extends('layouts.auth')

@section('title', 'Crear Cuenta - Hunabku')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

<div class="min-h-screen bg-[#1e293b] flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Patrón de fondo -->
    <div class="absolute inset-0 opacity-5 pointer-events-none"
        style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/svg%3E');">
    </div>

    <div
        class="w-full max-w-md bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl shadow-2xl p-8 relative z-10">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <svg width="60" height="60" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M60 10 C80 10, 95 30, 90 60 C85 85, 65 95, 55 90 C50 85, 60 60, 60 50 C60 25, 50 20, 60 10 Z"
                        fill="#ef4444" />
                    <path d="M10 30 C10 15, 30 10, 45 25 C55 35, 45 50, 40 50 C30 50, 10 45, 10 30 Z" fill="#fbbf24" />
                    <path d="M5 65 C5 50, 25 50, 40 60 C50 70, 40 85, 30 90 C15 95, 5 80, 5 65 Z" fill="#f97316" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white tracking-tight">Crear Cuenta</h2>
            <p class="text-gray-400 text-sm mt-2">Regístrate para acceder a Hunabku</p>
        </div>

        @if($errors->any())
        <div
            class="mb-6 bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-3 rounded-lg text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Nombre completo</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="Tu nombre completo"
                    class="w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    placeholder="tu-correo@ejemplo.com"
                    class="w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">Contraseña</label>
                <input type="password" id="password" name="password" required
                    placeholder="Mínimo 8 caracteres"
                    class="w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1.5">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    placeholder="Repite tu contraseña"
                    class="w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
            </div>

            <!-- Botón Crear Cuenta -->
            <button type="submit"
                class="flex items-center justify-center w-full py-3.5 px-4 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-all transform hover:-translate-y-0.5 shadow-lg shadow-red-500/30 mt-2">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Crear mi cuenta
            </button>
        </form>

        <!-- Separador -->
        <div class="flex items-center my-6">
            <div class="flex-1 border-t border-gray-600"></div>
            <span class="px-4 text-sm text-gray-400">o regístrate con</span>
            <div class="flex-1 border-t border-gray-600"></div>
        </div>

        <!-- Google -->
        <a href="{{ route('auth.redirect', 'google') }}"
            class="flex items-center justify-center w-full py-3.5 px-4 bg-white hover:bg-gray-50 text-gray-900 rounded-xl font-semibold transition-all transform hover:-translate-y-0.5 hover:shadow-lg">
            <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                <path fill="#4285F4"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                <path fill="#EA4335"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
            </svg>
            Continuar con Google
        </a>

        <!-- Link login -->
        <div class="flex items-center justify-center pt-6 border-t border-gray-700 mt-6">
            <a href="/"
                class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                ¿Ya tienes cuenta? <span class="text-red-400 font-medium">Iniciar sesión</span>
            </a>
        </div>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Hunabku. Todos los derechos reservados.</p>
        </div>
    </div>
</div>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
