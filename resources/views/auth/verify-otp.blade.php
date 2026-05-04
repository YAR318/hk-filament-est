@extends('layouts.auth')

@section('title', 'Verificar Código - Hunabku')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        .otp-input {
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

        <div
            class="w-full max-w-md bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl shadow-2xl p-8 relative z-10">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-500/20 rounded-full mb-4">
                    <svg class="w-8 h-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Verifica tu correo</h2>
                <p class="text-gray-400 text-sm mt-2">Ingresa el código de 6 dígitos enviado a<br><span
                        class="text-white font-medium">{{ $email }}</span></p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-300 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('dev_otp'))
                <div
                    class="mb-6 bg-yellow-500/10 border border-yellow-500/20 text-yellow-300 px-4 py-3 rounded-lg text-sm font-mono text-center">
                    🚀 MODO DESARROLLO: Tu código es <strong>{{ session('dev_otp') }}</strong>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-300 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <input type="text" name="otp" maxlength="6" required autofocus
                        class="otp-input w-full px-4 py-4 bg-[#0f172a]/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                        placeholder="000000">
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5">
                    Verificar Código
                </button>
            </form>

            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('otp.send') }}" class="inline">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="text-sm text-gray-400 hover:text-white transition-colors">
                        ¿No recibiste el código? <span class="text-red-400">Reenviar</span>
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-700 text-center">
                <a href="/" class="text-sm text-gray-400 hover:text-white transition-colors">
                    ← Volver al inicio
                </a>
            </div>
        </div>
    </div>
@endsection