<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.evolution.base_url');
        $this->apiKey = config('services.evolution.api_key');
    }

    /**
     * Enviar mensaje de texto
     * 
     * @param string $phoneNumber Número de teléfono
     * @param string $text Mensaje a enviar
     * @return array|bool Rerspuesta de la API o false si falló
     */
    public function sendMessage(string $phoneNumber, string $text)
    {
        try {
            // Eliminar caracteres no numéricos
            $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);

            // Endpoint para enviar texto (ajustar según la versión de Evolution API)
            // Asumiendo /message/sendText/{instance}
            // Pero Evolution API v2 suele ser POST /message/sendText
            // Necesitamos saber el nombre de la instancia. 
            // El usuario no lo proporcionó, pero su .env tiene META_WABA_ID.
            // Evolution API gestiona instancias. Asumiremos una instancia por defecto o global.
            // Si no hay instancia, intentaremos usar el endpoint genérico si existe o 'default'.

            $instanceName = config('services.evolution.instance', 'HunabkuBot');

            $url = "{$this->baseUrl}/message/sendText/{$instanceName}";

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'number' => $cleanPhone,
                'options' => [
                    'delay' => 1200,
                    'presence' => 'composing',
                    'linkPreview' => false
                ],
                'textMessage' => [
                    'text' => $text
                ]
            ]);

            if ($response->successful()) {
                Log::info('Mensaje enviado por Evolution API', ['phone' => $phoneNumber, 'response' => $response->json()]);
                return $response->json();
            }

            Log::error('Error al enviar mensaje por Evolution API', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return false;

        }
        catch (\Exception $e) {
            Log::error('Excepción al enviar mensaje por Evolution API: ' . $e->getMessage());
            return false;
        }
    }
}