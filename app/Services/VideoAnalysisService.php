<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class VideoAnalysisService
{
    protected string $apiKey;
    protected string $model = 'llama-3.3-70b-versatile';
    protected string $whisperModel = 'whisper-large-v3';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
    }

    /**
     * Extraer audio de un archivo de video usando FFmpeg
     *
     * @param string $videoPath Ruta absoluta al video
     * @return string Ruta al archivo de audio extraído (.mp3)
     */
    public function extractAudio(string $videoPath): string
    {
        $audioPath = preg_replace('/\.[^.]+$/', '.mp3', $videoPath);

        $result = Process::timeout(300)->run([
            'ffmpeg', '-i', $videoPath,
            '-vn',                    // sin video
            '-acodec', 'libmp3lame',  // codec MP3
            '-ab', '128k',            // bitrate
            '-ar', '16000',           // sample rate óptimo para Whisper
            '-ac', '1',               // mono
            '-y',                     // overwrite
            $audioPath,
        ]);

        if (!$result->successful()) {
            Log::error('VideoAnalysis: Error extrayendo audio', [
                'video' => $videoPath,
                'error' => $result->errorOutput(),
            ]);
            throw new \RuntimeException('Error al extraer audio del video: ' . $result->errorOutput());
        }

        if (!file_exists($audioPath)) {
            throw new \RuntimeException('El archivo de audio no fue creado.');
        }

        return $audioPath;
    }



    /**
     * Obtener la duración de un archivo multimedia usando FFprobe
     */
    public function getMediaDuration(string $filePath): ?int
    {
        try {
            $result = Process::timeout(10)->run([
                'ffprobe', '-v', 'error',
                '-show_entries', 'format=duration',
                '-of', 'default=noprint_wrappers=1:nokey=1',
                $filePath,
            ]);

            if ($result->successful()) {
                return (int) round((float) trim($result->output()));
            }
        } catch (\Exception $e) {
            Log::warning('VideoAnalysis: No se pudo obtener duración', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
