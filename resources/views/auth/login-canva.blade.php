@extends('layouts.auth')

@section('title', 'Acceso al Sistema - Hunabku')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

<div x-data="{ showEmailForm: {{ $errors->has('email') ? 'true' : 'false' }}, showOtherOptions: false, showLoginModal: {{ session('login_error') ? 'true' : 'false' }} }" class="min-h-screen bg-[#1e293b] flex items-center justify-center p-4 relative overflow-hidden">
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
            <h2 class="text-3xl font-bold text-white tracking-tight">hunabku</h2>
            <p class="text-gray-400 text-sm mt-2">Plataforma de Gestión Interna</p>
        </div>

        @if(session('error'))
        <div
            class="mb-6 bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-3 rounded-lg text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="space-y-4">
            <!-- Google Login -->
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

            <!-- Botón Usar mi correo -->
            <button @click="showEmailForm = !showEmailForm; showOtherOptions = false" type="button"
                class="flex items-center justify-center w-full py-3.5 px-4 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-all transform hover:-translate-y-0.5 shadow-lg shadow-red-500/30">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Usar mi correo
            </button>

            <!-- Formulario de correo (oculto inicialmente) -->
            <div x-show="showEmailForm" x-transition class="space-y-3 pt-2">
                <form method="POST" action="{{ route('otp.send') }}" class="space-y-3">
                    @csrf
                    <div>
                        <input type="email" name="email" required placeholder="tu-correo@ejemplo.com"
                            class="w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                        @error('email')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="flex items-center justify-center w-full py-3 px-4 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Enviar código a mi correo
                    </button>
                </form>
            </div>

            <!-- Botón Continuar de otra manera -->
            <button @click="showOtherOptions = !showOtherOptions; showEmailForm = false" type="button"
                class="flex items-center justify-center w-full py-3 px-4 bg-transparent border border-gray-600 text-gray-300 hover:text-white hover:border-white rounded-xl font-medium transition-colors">
                <span>Continuar de otra manera</span>
                <svg class="w-4 h-4 ml-2 transition-transform" :class="showOtherOptions ? 'rotate-180' : ''" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Opciones adicionales -->
            <div x-show="showOtherOptions" x-transition class="space-y-2">
                <a href="{{ route('auth.redirect', 'google') }}"
                    class="flex items-center px-4 py-3 bg-[#0f172a]/50 border border-gray-600 text-gray-300 hover:bg-gray-700 hover:text-white rounded-xl transition-colors">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                    Continuar con Google
                </a>
                <button @click="showEmailForm = !showEmailForm; showOtherOptions = false" type="button"
                    class="flex items-center w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 text-gray-300 hover:bg-gray-700 hover:text-white rounded-xl transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Usar mi correo
                </button>
                <button @click="showLoginModal = true" type="button"
                    class="flex items-center w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 text-gray-300 hover:bg-gray-700 hover:text-white rounded-xl transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Correo y contraseña
                </button>
            </div>

            <!-- Link registro -->
            <div class="flex items-center justify-center pt-4 border-t border-gray-700">
                <a href="{{ route('register') }}"
                    class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    ¿No tienes cuenta? <span class="text-red-400 font-medium">Crear cuenta</span>
                </a>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Hunabku. Todos los derechos reservados.</p>
        </div>
    </div>

    <!-- Modal: Correo y contraseña -->
    <div x-show="showLoginModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showLoginModal = false"></div>

        <!-- Modal Content -->
        <div x-show="showLoginModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md bg-[#1e293b] border border-white/10 rounded-2xl shadow-2xl p-8">
            <!-- Close button -->
            <button @click="showLoginModal = false" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Header -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-red-500/20 rounded-full mb-4">
                    <svg class="w-7 h-7 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Correo y contraseña</h3>
                <p class="text-gray-400 text-sm mt-1">Ingresa tus credenciales para acceder</p>
            </div>

            <!-- Form -->
            <form action="{{ route('login.manual') }}" method="POST" class="space-y-4">
                @csrf
                
                @if(session('login_error'))
                    <div class="bg-red-500/10 border border-red-500/20 text-red-300 px-4 py-3 rounded-lg text-sm">
                        {{ session('login_error') }}
                    </div>
                @endif

                <div>
                    <label for="modal-email" class="block text-sm font-medium text-gray-300 mb-1.5">Correo electrónico</label>
                    <input type="email" id="modal-email" name="email" value="{{ old('email') }}" required
                        placeholder="tu-correo@ejemplo.com"
                        class="w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                </div>
                <div>
                    <label for="modal-password" class="block text-sm font-medium text-gray-300 mb-1.5">Contraseña</label>
                    <input type="password" id="modal-password" name="password" required
                        placeholder="Tu contraseña"
                        class="w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                </div>
                <button type="submit"
                    class="flex items-center justify-center w-full py-3.5 px-4 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-all transform hover:-translate-y-0.5 shadow-lg shadow-red-500/30">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Iniciar sesión
                </button>
            </form>

            <!-- Olvidé mi contraseña -->
            <div class="mt-5 pt-4 border-t border-gray-700 text-center">
                <a href="{{ route('password.forgot') }}" class="text-sm text-gray-400 hover:text-amber-400 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <div class="mt-3 text-center">
                <button @click="showLoginModal = false" type="button" class="text-sm text-gray-500 hover:text-white transition-colors">
                    ← Volver
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection