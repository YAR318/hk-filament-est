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
Route::post('/messages', [MessageController::class , 'store']);

// Rutas para historial de chat
Route::post('/chat/history', [ChatHistoryController::class , 'getHistory']);
Route::get('/chat-history/{phone}', [ChatHistoryController::class , 'getHistoryByPhone']);
Route::post('/chat/save-response', [ChatHistoryController::class , 'saveAssistantResponse']);
Route::post('/chat/process-message', [\App\Http\Controllers\Api\ChatHistoryController::class , 'processIncomingMessage']);

// Verificar si un número está bloqueado
Route::get('/whatsapp/check-block/{phone}', [\App\Http\Controllers\Api\BlockedPhoneController::class , 'check']);

// Verificar si el bot está activo para un número
Route::get('/bot/status/{phone}', [\App\Http\Controllers\Api\BotStatusController::class , 'check']);

// Rutas para operadores
Route::get('/operators/check/{phoneNumber}', [OperatorController::class , 'check']);
Route::post('/operators/update-last-message', [OperatorController::class , 'updateLastMessage']);

// Ruta de health check
Route::get('/health', function () {
    return response()->json([
    'status' => 'ok',
    'project' => 'HK_Filament_EST',
    'timestamp' => now()->toDateTimeString()
    ]);
});