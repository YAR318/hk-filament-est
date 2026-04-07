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

            // Paso 2: Transcribir
            $analysis->update(['status' => 'transcribing']);
            $transcription = $service->transcribeAudio($audioPath);

            if (empty(trim($transcription))) {
                throw new \RuntimeException('No se pudo transcribir el audio. El archivo podría no contener voz.');
            }

            $analysis->update(['transcription' => $transcription]);

            // Paso 3: Generar minutas con IA
            $analysis->update(['status' => 'analyzing']);
            $minutes = $service->generateMinutes($transcription);

            // Paso 4: Guardar resultados
            $analysis->update([
                'summary' => $minutes['summary'],
                'key_decisions' => $minutes['key_decisions'],
                'tasks' => $minutes['tasks'],
                'status' => 'completed',
            ]);

            Log::info('ProcessVideoAnalysis: Completado', [
                'id' => $analysis->id,
                'duration' => $duration,
                'transcription_length' => mb_strlen($transcription),
                'tasks_count' => count($minutes['tasks']),
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
