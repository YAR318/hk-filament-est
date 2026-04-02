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

                // getTextBody/getHTMLBody pueden ser Attribute, convertir a string
                $body = '';
                try {
                    $textBody = $message->getTextBody();
                    $htmlBody = $message->getHTMLBody();
                    $body = is_object($textBody) ? (string) $textBody : ($textBody ?? '');
                    if (empty($body)) {
                        $body = is_object($htmlBody) ? (string) $htmlBody : ($htmlBody ?? '');
                    }
                } catch (\Exception $e) {
                    $body = '';
                }
                $body = strip_tags($body);
                $snippet = mb_substr(trim($body), 0, 500);

                // getFrom() retorna Attribute, no array
                $fromEmail = '';
                $fromName = '';
                try {
                    $from = $message->getFrom();
                    if ($from) {
                        // Attribute tiene ->first() o se puede iterar
                        $firstFrom = is_object($from) ? $from->first() : ($from[0] ?? null);
                        if ($firstFrom) {
                            $fromEmail = $firstFrom->mail ?? '';
                            $fromName = $firstFrom->personal ?? $fromEmail;
                        }
                    }
                } catch (\Exception $e) {
                    $fromEmail = 'desconocido';
                    $fromName = 'Desconocido';
                }

                // getSubject() y getDate() también retornan Attribute
                $subject = '';
                try {
                    $subj = $message->getSubject();
                    $subject = is_object($subj) ? (string) $subj : ($subj ?? '(Sin asunto)');
                } catch (\Exception $e) {
                    $subject = '(Sin asunto)';
                }

                $dateStr = '';
                try {
                    $date = $message->getDate();
                    if ($date) {
                        $dateObj = is_object($date) ? $date->first() : $date;
                        $dateStr = ($dateObj instanceof \Carbon\Carbon || $dateObj instanceof \DateTime)
                            ? $dateObj->format('H:i')
                            : (string) $date;
                    }
                } catch (\Exception $e) {
                    $dateStr = '';
                }

                $emails[] = [
                    'subject' => $subject ?: '(Sin asunto)',
                    'from_name' => $fromName ?: 'Desconocido',
                    'from_email' => $fromEmail,
                    'date' => $dateStr,
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
