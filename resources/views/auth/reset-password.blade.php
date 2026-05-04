@extends('layouts.auth')

@section('title', 'Nueva Contraseña - Hunabku')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Poppins', sans-serif; }
    .token-input {
        letter-spacing: 0.5em;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 600;
    }
</style>

<div class="min-h-screen bg-[#1e293b] flex items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5 pointer-events-none"
        style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/svg%3E');">
    </div>

    <div class="w-full max-w-md bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl shadow-2xl p-8 relative z-10">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-500/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white">Crea tu nueva contraseña</h2>
            <p class="text-gray-400 text-sm mt-2">Ingresa el código enviado a<br><span class="text-white font-medium">{{ $email }}</span></p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-300 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-300 px-4 py-3 rounded-lg text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label for="token" class="block text-sm font-medium text-gray-300 mb-1.5">Código de verificación</label>
                <input type="text" name="token" id="token" maxlength="6" required autofocus
                    class="token-input w-full px-4 py-4 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all"
                    placeholder="000000">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Nueva Contraseña</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all"
                    placeholder="Mínimo 8 caracteres">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-4 py-3 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all"
                    placeholder="Repite tu contraseña">
            </div>

            <button type="submit"
                class="w-full py-3.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 transition-all transform hover:-translate-y-0.5">
                Guardar nueva contraseña
            </button>
        </form>

        <div class="mt-6 text-center">
            <form method="POST" action="{{ route('password.send-reset') }}" class="inline">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="text-sm text-gray-400 hover:text-white transition-colors">
                    ¿No recibiste el código? <span class="text-amber-400">Reenviar</span>
                </button>
            </form>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-700 text-center">
            <a href="/" class="text-sm text-gray-400 hover:text-white transition-colors">
                ← Volver al inicio
            </a>
        </div>
    </div>
</div>
@endsection
