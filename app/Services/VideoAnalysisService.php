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
     * Transcribir audio usando Groq Whisper API
     *
     * @param string $audioPath Ruta absoluta al archivo de audio
     * @return string Transcripción completa
     */
    public function transcribeAudio(string $audioPath): string
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('GROQ_API_KEY no está configurada en .env');
        }

        // Groq Whisper tiene un límite de 25MB por archivo
        $fileSize = filesize($audioPath);
        if ($fileSize > 25 * 1024 * 1024) {
            // Si es mayor a 25MB, dividir y transcribir por partes
            return $this->transcribeLargeAudio($audioPath);
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->timeout(120)->attach(
            'file', file_get_contents($audioPath), basename($audioPath)
        )->post('https://api.groq.com/openai/v1/audio/transcriptions', [
            'model' => $this->whisperModel,
            'language' => 'es',
            'response_format' => 'verbose_json',
        ]);

        if (!$response->successful()) {
            Log::error('VideoAnalysis: Error en Groq Whisper', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Error al transcribir audio: ' . $response->status());
        }

        $data = $response->json();
        return $data['text'] ?? '';
    }

    /**
     * Transcribir audio largo (>25MB) dividiéndolo en partes
     */
    protected function transcribeLargeAudio(string $audioPath): string
    {
        $dir = dirname($audioPath);
        $basename = pathinfo($audioPath, PATHINFO_FILENAME);

        // Dividir en segmentos de 10 minutos
        $result = Process::timeout(300)->run([
            'ffmpeg', '-i', $audioPath,
            '-f', 'segment',
            '-segment_time', '600',
            '-c:a', 'libmp3lame',
            '-ar', '16000',
            '-ac', '1',
            '-y',
            "{$dir}/{$basename}_part%03d.mp3",
        ]);

        if (!$result->successful()) {
            throw new \RuntimeException('Error al dividir el audio: ' . $result->errorOutput());
        }

        // Encontrar las partes
        $parts = glob("{$dir}/{$basename}_part*.mp3");
        sort($parts);

        $fullTranscription = '';
        foreach ($parts as $part) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                ])->timeout(120)->attach(
                    'file', file_get_contents($part), basename($part)
                )->post('https://api.groq.com/openai/v1/audio/transcriptions', [
                    'model' => $this->whisperModel,
                    'language' => 'es',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $fullTranscription .= ($data['text'] ?? '') . ' ';
                }
            } finally {
                @unlink($part); // Limpiar parte temporal
            }
        }

        return trim($fullTranscription);
    }

    /**
     * Generar minutas, decisiones y tareas a partir de una transcripción
     *
     * @param string $transcription Texto transcrito
     * @return array{summary: string, key_decisions: array, tasks: array}
     */
    public function generateMinutes(string $transcription): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('GROQ_API_KEY no está configurada en .env');
        }

        $truncated = mb_substr($transcription, 0, 12000);
        if (mb_strlen($transcription) > 12000) {
            $truncated .= "\n\n[... Transcripcion truncada por longitud ...]";
        }

        $systemPrompt = <<<PROMPT
Eres un asistente ejecutivo que genera minutas de reuniones. Tu trabajo es analizar la transcripcion de una reunion y generar un resumen estructurado.

REGLAS ESTRICTAS:
- Responde SIEMPRE en español.
- Tu respuesta debe ser un JSON valido con esta estructura exacta:
{
  "summary": "Resumen ejecutivo de la reunion en 3-5 oraciones.",
  "key_decisions": [
    "Decision 1 que se tomo en la reunion",
    "Decision 2"
  ],
  "tasks": [
    {"task": "Descripcion de la tarea", "assignee": "Persona responsable o 'Sin asignar'", "done": false},
    {"task": "Otra tarea", "assignee": "Sin asignar", "done": false}
  ]
}
- El resumen debe capturar los temas principales discutidos.
- Las decisiones son acuerdos o resoluciones tomadas. Si no hay decisiones claras, pon un array vacio.
- Las tareas son compromisos o acciones a realizar. Intenta identificar quien es responsable segun el contexto.
- Se conciso y profesional.
- NO uses emojis.
- SOLO responde con el JSON.
PROMPT;

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Analiza la siguiente transcripcion de reunion:\n\n{$truncated}"],
            ],
            'temperature' => 0.2,
            'max_tokens' => 2500,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!$response->successful()) {
            Log::error('VideoAnalysis: Error en Groq API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Error al generar minutas: ' . $response->status());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '{}';
        $parsed = json_decode($content, true);

        if (!$parsed || !isset($parsed['summary'])) {
            return ['summary' => $content, 'key_decisions' => [], 'tasks' => []];
        }

        return [
            'summary' => $parsed['summary'],
            'key_decisions' => $parsed['key_decisions'] ?? [],
            'tasks' => $parsed['tasks'] ?? [],
        ];
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
