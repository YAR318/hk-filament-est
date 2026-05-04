<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Hunabku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-[#1e293b] flex items-center justify-center min-h-screen p-4 relative overflow-hidden">

    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5 pointer-events-none"
        style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/svg%3E');">
    </div>

    <div
        class="w-full max-w-md bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl shadow-2xl p-8 relative z-10 text-center">

        <!-- Avatar/User Info -->
        <div class="mb-6 flex justify-center">
            <div
                class="w-20 h-20 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-3xl font-bold text-white shadow-lg">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        </div>

        <h2 class="text-2xl font-bold text-white mb-2">¡Hola, {{ Auth::user()->name }}!</h2>
        <p class="text-gray-400 text-sm mb-8">Ya has iniciado sesión en el sistema.</p>

        <div class="space-y-4">
            <!-- Botón Ir a Perfil -->
            <a href="/profile"
                class="block w-full py-3.5 bg-[#ff5a5f] hover:bg-[#e04e53] text-white font-bold rounded-xl shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Ir a mi Perfil
            </a>

            <!-- Botón Cerrar Sesión -->
            <form method="POST" action="{{ route('profile.logout') }}">
                @csrf
                <button type="submit"
                    class="block w-full py-3.5 bg-gray-700/50 hover:bg-gray-700 text-gray-300 font-semibold rounded-xl border border-gray-600 transition-all flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Cerrar Sesión
                </button>
            </form>
        </div>

        <div class="mt-8 text-xs text-gray-500">
            Hunabku Systems
        </div>
    </div>
</body>

</html>