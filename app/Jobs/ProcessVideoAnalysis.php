<?php

namespace App\Jobs;

use App\Models\VideoAnalysis;
use App\Services\VideoAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoAnalysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600; // 10 minutos máximo
    public int $tries = 1;     // No reintentar

    public function __construct(
        public int $analysisId
    ) {}

    public function handle(): void
    {
        $analysis = VideoAnalysis::find($this->analysisId);

        if (!$analysis) {
            Log::error('ProcessVideoAnalysis: Análisis no encontrado', ['id' => $this->analysisId]);
            return;
        }

        $service = new VideoAnalysisService();

        try {
            $fullPath = Storage::disk('public')->path($analysis->file_path);

            // Obtener duración del medio
            $duration = $service->getMediaDuration($fullPath);
            $analysis->update(['duration_seconds' => $duration]);

            // Paso 1: Extraer audio (si es video)
            $audioPath = $fullPath;
            $isVideo = in_array($analysis->file_type, ['mp4', 'webm', 'mov', 'avi', 'mkv']);

            if ($isVideo) {
                $analysis->update(['status' => 'extracting_audio']);
                $audioPath = $service->extractAudio($fullPath);

                // Guardar referencia al audio
                $relativePath = str_replace(Storage::disk('public')->path(''), '', $audioPath);
                $analysis->update(['audio_path' => ltrim($relativePath, '/')]);
            }

            // Paso 2: Transcribir el audio usando Groq (vía llamada nativa)
            $apiKey = env('GROQ_API_KEY');
            if (!$apiKey) { throw new \RuntimeException('No existe GROQ_API_KEY en el .env'); }
            
            $transcriptionResponse = \Illuminate\Support\Facades\Http::timeout(300)
                ->withToken($apiKey)
                ->attach('file', file_get_contents(Storage::disk('public')->path($analysis->audio_path)), 'audio.mp3')
                ->post('https://api.groq.com/openai/v1/audio/transcriptions', [
                    'model' => 'whisper-large-v3',
                    'response_format' => 'json',
                    'language' => 'es',
                ]);
                
            if (!$transcriptionResponse->successful()) {
                throw new \RuntimeException('Error en Groq API: ' . $transcriptionResponse->body());
            }
            $transcriptionText = $transcriptionResponse->json('text', '');

            // Paso 3: Notificar a n8n para que genere resumen y tareas
            $n8nUrl = env('N8N_WEBHOOK_URL', 'http://n8n:5678') . '/webhook/video-analyzer';

            $response = \Illuminate\Support\Facades\Http::timeout(30)->post($n8nUrl, [
                'analysis_id' => $analysis->id,
                'transcription' => $transcriptionText,
            ]);

            if (!$response->successful()) {
                throw new \RuntimeException('No se pudo contactar al webhook de n8n.');
            }

            // El Job de Laravel termina aquí. n8n actualizará el estado más tarde.
            Log::info('ProcessVideoAnalysis: Audio enviado a n8n', [
                'id' => $analysis->id,
                'duration' => $duration,
            ]);

        } catch (\Exception $e) {
            Log::error('ProcessVideoAnalysis: Error', [
                'id' => $analysis->id,
                'error' => $e->getMessage(),
            ]);

            $analysis->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
