<?php

use App\Http\Controllers\Api\OperatorController;
use App\Http\Controllers\Api\ChatHistoryController;
use App\Http\Controllers\Api\BlockedPhoneController;
use App\Http\Controllers\Api\BotStatusController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\MetaWebhookController;
use App\Http\Controllers\Api\KnowledgeBaseController;
use Illuminate\Support\Facades\Route;

/*
 |--------------------------------------------------------------------------
 | API Routes - HK_Filament_EST
 |--------------------------------------------------------------------------
 |
 | API de datos para n8n. Laravel solo provee y almacena información.
 | Toda la orquestación (AI, envío de mensajes, etc.) la maneja n8n.
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

// Webhook de Meta WhatsApp (público, sin api.key)
Route::get('/webhook/meta', [MetaWebhookController::class, 'verify']);
Route::post('/webhook/meta', [MetaWebhookController::class, 'receive']);

// Rutas protegidas con API Key + Rate Limiting
Route::middleware(['api.key', 'throttle:60,1'])->group(function () {

    // ─── Historial de Chat (n8n guarda/consulta mensajes) ────────────
    Route::post('/chat/history', [ChatHistoryController::class, 'getHistory']);
    Route::get('/chat-history/{phone}', [ChatHistoryController::class, 'getHistoryByPhone']);
    Route::post('/chat/save-response', [ChatHistoryController::class, 'saveAssistantResponse']);
    Route::post('/chat/process-message', [ChatHistoryController::class, 'processIncomingMessage']);
    Route::post('/chat/escalate', [ChatHistoryController::class, 'escalateToHuman']);

    // ─── Verificaciones (n8n consulta estado) ────────────────────────
    Route::get('/whatsapp/check-block/{phone}', [BlockedPhoneController::class, 'check']);
    Route::get('/bot/status/{phone}', [BotStatusController::class, 'check']);
    Route::get('/operators/check/{phoneNumber}', [OperatorController::class, 'check']);

    // ─── Citas (CRUD para n8n) ──────────────────────────────────────
    Route::post('/appointments/create', [AppointmentController::class, 'store']);
    Route::post('/appointments/cancel', [AppointmentController::class, 'cancel']);
    Route::post('/appointments/reschedule', [AppointmentController::class, 'reschedule']);
    Route::post('/appointments/sync-calendar', [AppointmentController::class, 'syncCalendar']);
    Route::get('/appointments/next-slots', [AppointmentController::class, 'nextAvailable']);
    Route::get('/appointments/available/{date}', [AppointmentController::class, 'available']);

    // ─── Base de conocimiento (contexto para el bot) ────────────────
    Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index']);

    // ─── Actualizaciones asíncronas de n8n ──────────────────────────
    Route::post('/video-analysis/update', [\App\Http\Controllers\Api\VideoAnalysisController::class, 'updateFromN8n']);

});