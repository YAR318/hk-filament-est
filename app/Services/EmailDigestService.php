<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Facades\Client as ImapClient;

class EmailDigestService
{
    /**
     * Fetch today's emails from Gmail via IMAP.
     *
     * @return array List of emails with subject, from, date, and snippet.
     */
    public function fetchTodaysEmails(): array
    {
        try {
            $client = ImapClient::account('default');
            $client->connect();

            $folder = $client->getFolder('INBOX');

            $today = now()->format('d-M-Y');

            $messages = $folder->query()
                ->since($today)
                ->get();

            $emails = [];
            $count = 0;

            foreach ($messages as $message) {
                if ($count >= 50) break; // Limitar a 50 correos

                $body = $message->getTextBody() ?? $message->getHTMLBody() ?? '';
                // Limpiar HTML si es necesario
                $body = strip_tags($body);
                // Limitar el body a 500 caracteres por correo
                $snippet = mb_substr(trim($body), 0, 500);

                $from = $message->getFrom();
                $fromEmail = '';
                $fromName = '';
                if ($from && count($from) > 0) {
                    $firstFrom = $from[0];
                    $fromEmail = $firstFrom->mail ?? '';
                    $fromName = $firstFrom->personal ?? $fromEmail;
                }

                $emails[] = [
                    'subject' => $message->getSubject() ?? '(Sin asunto)',
                    'from_name' => $fromName,
                    'from_email' => $fromEmail,
                    'date' => $message->getDate()?->format('H:i') ?? '',
                    'snippet' => $snippet,
                ];

                $count++;
            }

            $client->disconnect();

            return $emails;
        } catch (\Exception $e) {
            Log::error('EmailDigest: Error al leer correos IMAP', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate AI summary from a list of emails using Groq.
     *
     * @param array $emails
     * @return string AI-generated summary in Markdown
     */
    public function generateDigest(array $emails): string
    {
        if (empty($emails)) {
            return "📭 No se encontraron correos nuevos el día de hoy.";
        }

        $apiKey = config('services.groq.api_key');

        if (!$apiKey) {
            throw new \RuntimeException('GROQ_API_KEY no está configurada en .env');
        }

        // Formatear los correos como texto para el prompt
        $emailsText = '';
        foreach ($emails as $i => $email) {
            $num = $i + 1;
            $emailsText .= "--- Correo #{$num} ---\n";
            $emailsText .= "De: {$email['from_name']} <{$email['from_email']}>\n";
            $emailsText .= "Asunto: {$email['subject']}\n";
            $emailsText .= "Hora: {$email['date']}\n";
            $emailsText .= "Contenido: {$email['snippet']}\n\n";
        }

        $systemPrompt = <<<PROMPT
Eres un asistente ejecutivo que resume correos electrónicos. Tu trabajo es leer una lista de correos recibidos en un día y generar un resumen ejecutivo claro y útil.

REGLAS:
- Responde SIEMPRE en español.
- Usa formato Markdown.
- Categoriza los correos en estas secciones (si aplica):
  🔴 **Urgente / Requiere Acción**: Correos que necesitan respuesta o acción inmediata.
  📋 **Informativo**: Notificaciones, actualizaciones, informes que solo necesitan lectura.
  🛒 **Promocional / Marketing**: Ofertas, newsletters, publicidad.
  🤖 **Automatizado / Sistema**: Notificaciones automáticas, alertas de servidores, confirmaciones.
- Al final, agrega una sección "📊 Estadísticas del Día" con el total de correos por categoría.
- Sé conciso pero informativo. Máximo 2 líneas por correo resumido.
- Si un correo es spam o irrelevante, agrúpalo brevemente sin detallar.
PROMPT;

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Aquí están los correos recibidos hoy (" . now()->format('d/m/Y') . "):\n\n{$emailsText}\n\nGenera el resumen ejecutivo."],
            ],
            'temperature' => 0.3,
            'max_tokens' => 2000,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? 'No se pudo generar el resumen.';
        }

        Log::error('EmailDigest: Error en Groq API', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        throw new \RuntimeException('Error al comunicarse con la IA: ' . $response->status());
    }
}
