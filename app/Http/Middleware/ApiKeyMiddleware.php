<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Validar que la petición incluya una API Key válida.
     *
     * Se acepta en:
     * - Header: Authorization: Bearer {key}
     * - Header: X-API-Key: {key}
     *
     * La clave se compara contra API_INTERNAL_KEY en .env
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.api_key');

        // Si no hay API key configurada, permitir acceso (desarrollo local)
        if (empty($apiKey)) {
            return $next($request);
        }

        $providedKey = $request->header('X-API-Key')
            ?? $request->bearerToken();

        if (!$providedKey || $providedKey !== $apiKey) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'API key inválida o no proporcionada.',
            ], 401);
        }

        return $next($request);
    }
}