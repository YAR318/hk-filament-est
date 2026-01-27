@extends('layouts.auth')

@section('title', 'Usar mi correo personal')

@section('content')
<div class="flex min-h-screen flex-col items-center justify-center p-6">
    <div class="w-full max-w-md">
        <main class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 px-8 py-10">
            
            <div class="mb-6">
                <a href="/" class="inline-flex items-center text-sm text-gray-700 hover:text-gray-950 transition-colors">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Volver</span>
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight text-gray-950">
                    Usar mi correo personal
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Verificaremos si tienes una cuenta y, si no la tienes, te ayudaremos a crear una.
                </p>
            </div>

            <form action="/admin/login" method="GET" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-950 mb-2">
                        Correo electrónico (personal o laboral)
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        required
                        placeholder="nombre@ejemplo.com"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-950 placeholder:text-gray-400 px-3 py-2 text-sm"
                    >
                </div>

                <button 
                    type="submit"
                    class="w-full px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-semibold text-sm rounded-lg shadow-sm transition-colors duration-150">
                    Continuar
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-gray-500">
                Al continuar, aceptas las 
                <a href="#" class="text-amber-600 hover:text-amber-500 underline">Condiciones de uso</a> de
                {{ config('app.name') }}. Consulta nuestra 
                <a href="#" class="text-amber-600 hover:text-amber-500 underline">Política de privacidad</a>.
            </div>
        </main>
    </div>
</div>
@endsection
