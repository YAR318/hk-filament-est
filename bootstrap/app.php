<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Excluir rutas API del CSRF para permitir requests desde n8n
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // Sesión única: invalidar sesiones anteriores al iniciar sesión nueva
        $middleware->web(append: [
            \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        // Registrar alias para middleware de API Key
        $middleware->alias([
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();