<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppProviderInterface;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Proveedor WhatsApp usando Meta Cloud API (oficial).
 * Usa Graph API v21.0 para enviar mensajes.
 */
class MetaWhatsAppProvider implements WhatsAppProviderInterface
{
    protected ?string $phoneNumberId;
    protected ?string $accessToken;

    public function __construct()
    {
        $this->phoneNumberId = AppSetting::get('meta_phone_id');
        $this->accessToken = AppSetting::get('meta_access_token');
    }

    public function sendMessage(string $phoneNumber, string $text): array
    {
        if (!$this->isConfigured()) {
            Log::warning('MetaWhatsAppProvider: No configurado');
            return [
                'success' => false,
                'provider' => 'meta',
                'error' => 'Meta API no está configurada',
            ];
        }

        // Formatear número: Meta necesita formato sin + ni espacios
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);

        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(10)
                ->retry(3, 100)
                ->post("https://graph.facebook.com/v21.0/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $phone,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $text,
                    ],
                ]);

            if ($response->successful()) {
                Log::info('MetaWhatsAppProvider: Mensaje enviado', [
                    'phone' => $phone,
                    'message_id' => $response->json('messages.0.id'),
                ]);
                return [
                    'success' => true,
                    'provider' => 'meta',
                    'data' => $response->json(),
                ];
            }

            Log::warning('MetaWhatsAppProvider: Error en respuesta', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return [
                'success' => false,
                'provider' => 'meta',
                'error' => $response->json('error.message', 'Error desconocido'),
            ];

        } catch (\Exception $e) {
            Log::error('MetaWhatsAppProvider: Excepcion enviando mensaje', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'provider' => 'meta',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getProviderName(): string
    {
        return 'meta';
    }

    public function isConfigured(): bool
    {
        return !empty($this->phoneNumberId) && !empty($this->accessToken);
    }
}