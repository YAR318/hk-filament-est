<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OperatorController;
use App\Http\Controllers\Api\ChatHistoryController;
use App\Http\Controllers\Api\BlockedPhoneController;
use App\Http\Controllers\Api\BotStatusController;
use App\Http\Controllers\Api\AppointmentController;
use Illuminate\Support\Facades\Route;

/*
 |--------------------------------------------------------------------------
 | API Routes - HK_Filament_EST
 |--------------------------------------------------------------------------
 |
 | Rutas para recibir mensajes de WhatsApp desde n8n (Meta API)
 | Protegidas con API Key (API_INTERNAL_KEY) y rate limiting
 |
 */

// Ruta de health check (sin autenticación)
Route::get('/health', function () {
    return response()->json([
    'status' => 'ok',
    'project' => 'HK_Filament_EST',
    'timestamp' => now()->toDateTimeString()
    ]);
});

// Rutas protegidas con API Key + Rate Limiting
Route::middleware(['api.key', 'throttle:60,1'])->group(function () {

    // Ruta para recibir mensajes de WhatsApp desde n8n
    Route::post('/messages', [MessageController::class , 'store']);

    // Rutas para historial de chat
    Route::post('/chat/history', [ChatHistoryController::class , 'getHistory']);
    Route::get('/chat-history/{phone}', [ChatHistoryController::class , 'getHistoryByPhone']);
    Route::post('/chat/save-response', [ChatHistoryController::class , 'saveAssistantResponse']);
    Route::post('/chat/process-message', [ChatHistoryController::class , 'processIncomingMessage']);

    // Verificar si un número está bloqueado
    Route::get('/whatsapp/check-block/{phone}', [BlockedPhoneController::class , 'check']);

    // Verificar si el bot está activo para un número
    Route::get('/bot/status/{phone}', [BotStatusController::class , 'check']);

    // Rutas para operadores
    Route::get('/operators/check/{phoneNumber}', [OperatorController::class , 'check']);
    Route::post('/operators/update-last-message', [OperatorController::class , 'updateLastMessage']);

    // Rutas para citas (Google Calendar)
    Route::post('/appointments/create', [AppointmentController::class , 'store']);
    Route::post('/appointments/cancel', [AppointmentController::class , 'cancel']);
    Route::post('/appointments/reschedule', [AppointmentController::class , 'reschedule']);
    Route::get('/appointments/available/{date}', [AppointmentController::class , 'available']);

});