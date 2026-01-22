<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AuthorizedUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - HK_Filament_EST
|--------------------------------------------------------------------------
|
| Rutas para recibir mensajes de WhatsApp desde n8n (Meta API)
|
*/

// Ruta para recibir mensajes de WhatsApp desde n8n
Route::post('/messages', [MessageController::class, 'store']);

// Rutas para usuarios autorizados
Route::get('/authorized-users/check/{phoneNumber}', [AuthorizedUserController::class, 'check']);
Route::post('/authorized-users/update-last-message', [AuthorizedUserController::class, 'updateLastMessage']);

// Ruta de health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'project' => 'HK_Filament_EST',
        'timestamp' => now()->toDateTimeString()
    ]);
});
