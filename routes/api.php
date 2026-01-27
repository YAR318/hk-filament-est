<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OperatorController;
use App\Http\Controllers\Api\ChatHistoryController;
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

// Rutas para historial de chat
Route::post('/chat/history', [ChatHistoryController::class, 'getHistory']);
Route::get('/chat-history/{phone}', [ChatHistoryController::class, 'getHistoryByPhone']);
Route::post('/chat/save-response', [ChatHistoryController::class, 'saveAssistantResponse']);

// Rutas para operadores
Route::get('/operators/check/{phoneNumber}', [OperatorController::class, 'check']);
Route::post('/operators/update-last-message', [OperatorController::class, 'updateLastMessage']);

// Ruta de health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'project' => 'HK_Filament_EST',
        'timestamp' => now()->toDateTimeString()
    ]);
});
