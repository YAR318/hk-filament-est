<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DocumentAnalysisService
{
    protected string $apiKey;
    protected string $model = 'llama-3.3-70b-versatile';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
    }

    /**
     * Generar resumen y puntos clave de un documento
     *
     * @param string $text Texto extraído del documento
     * @return array{summary: string, key_points: array}
     */
    public function generateSummary(string $text): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('GROQ_API_KEY no está configurada en .env');
        }

        // Limitar texto a ~12000 caracteres para no exceder el contexto del LLM
        $truncatedText = mb_substr($text, 0, 12000);
        if (mb_strlen($text) > 12000) {
            $truncatedText .= "\n\n[... Documento truncado por longitud. Se analizaron los primeros 12,000 caracteres ...]";
        }

        $systemPrompt = <<<PROMPT
Eres un asistente empresarial que analiza documentos. Tu trabajo es leer el contenido de un documento y generar un analisis completo.

REGLAS ESTRICTAS:
- Responde SIEMPRE en español.
- Tu respuesta debe ser un JSON valido con esta estructura exacta:
{
  "summary": "Resumen ejecutivo del documento en 3-5 oraciones claras y concisas.",
  "key_points": [
    "Punto clave 1",
    "Punto clave 2",
    "Punto clave 3"
  ]
}
- El resumen debe ser claro, profesional y util para alguien que no ha leido el documento.
- Los puntos clave deben ser entre 3 y 8 items, cada uno en una oracion concisa.
- NO uses emojis ni caracteres especiales.
- SOLO responde con el JSON, sin texto adicional antes ni despues.
PROMPT;

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Analiza el siguiente documento:\n\n{$truncatedText}"],
            ],
            'temperature' => 0.2,
            'max_tokens' => 2000,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!$response->successful()) {
            Log::error('DocumentAnalysis: Error en Groq API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Error al comunicarse con la IA: ' . $response->status());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '{}';

        $parsed = json_decode($content, true);

        if (!$parsed || !isset($parsed['summary'])) {
            Log::warning('DocumentAnalysis: Respuesta no parseable', ['content' => $content]);
            return [
                'summary' => $content,
                'key_points' => [],
            ];
        }

        return [
            'summary' => $parsed['summary'],
            'key_points' => $parsed['key_points'] ?? [],
        ];
    }

    /**
     * Chat con el documento — Enviar una pregunta sobre el contenido
     *
     * @param string $documentText Texto completo del documento
     * @param string $question Pregunta del usuario
     * @param array $chatHistory Historial previo de chat [{role, content}]
     * @return string Respuesta de la IA
     */
    public function chatWithDocument(string $documentText, string $question, array $chatHistory = []): string
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('GROQ_API_KEY no está configurada en .env');
        }

        // Limitar contexto del documento
        $truncatedText = mb_substr($documentText, 0, 10000);

        $systemPrompt = <<<PROMPT
Eres un asistente que responde preguntas sobre un documento especifico. Tienes acceso al contenido del documento y debes basar tus respuestas UNICAMENTE en la informacion contenida en el mismo.

REGLAS:
- Responde SIEMPRE en español.
- Se conciso y directo.
- Si la informacion no esta en el documento, dilo claramente: "Esta informacion no se encuentra en el documento."
- NO inventes informacion que no este en el documento.
- Usa formato Markdown si es util (listas, negritas, etc.)
- NO uses emojis.

CONTENIDO DEL DOCUMENTO:
---
{$truncatedText}
---
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Agregar historial de chat (últimos 10 mensajes)
        $recentHistory = array_slice($chatHistory, -10);
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        // Agregar la pregunta actual
        $messages[] = ['role' => 'user', 'content' => $question];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.3,
            'max_tokens' => 1500,
        ]);

        if (!$response->successful()) {
            Log::error('DocumentAnalysis Chat: Error en Groq API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Error al comunicarse con la IA.');
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? 'No se pudo generar una respuesta.';
    }
}
