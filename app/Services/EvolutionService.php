<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $instanceName;

    public function __construct()
    {
        $this->baseUrl = config('services.evolution.base_url');
        $this->apiKey = config('services.evolution.api_key');
        $this->instanceName = config('services.evolution.instance', 'HunabkuBot');
    }

    /**
     * Get the connection state of the instance
     */
    public function getConnectionState(): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get("{$this->baseUrl}/instance/connectionState/{$this->instanceName}");

            if ($response->successful()) {
                return $response->json();
            }

            return ['instance' => ['state' => 'unknown']];
        }
        catch (\Exception $e) {
            Log::error('Error al consultar estado de conexión: ' . $e->getMessage());
            return ['instance' => ['state' => 'error']];
        }
    }

    /**
     * Get the QR code for connecting (only works when disconnected)
     */
    public function getConnectQR(): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get("{$this->baseUrl}/instance/connect/{$this->instanceName}");

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'No se pudo obtener el QR', 'status' => $response->status()];
        }
        catch (\Exception $e) {
            Log::error('Error al obtener QR: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get instance details (profile, status, etc.)
     */
    public function getInstanceInfo(): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get("{$this->baseUrl}/instance/fetchInstances", [
                'instanceName' => $this->instanceName,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!is_array($data)) {
                    return [];
                }

                // If it's an array of instances, get the first one
                if (isset($data[0])) {
                    return $data[0];
                }

                // If it has 'instance' key directly (single instance response)
                if (isset($data['instance'])) {
                    return $data;
                }

                return $data;
            }

            return [];
        }
        catch (\Exception $e) {
            Log::error('Error al obtener info de instancia: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Disconnect/logout the instance
     */
    public function disconnect(): bool
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->delete("{$this->baseUrl}/instance/logout/{$this->instanceName}");

            return $response->successful();
        }
        catch (\Exception $e) {
            Log::error('Error al desconectar instancia: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restart the instance
     */
    public function restart(): bool
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->put("{$this->baseUrl}/instance/restart/{$this->instanceName}");

            return $response->successful();
        }
        catch (\Exception $e) {
            Log::error('Error al reiniciar instancia: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje de texto
     */
    public function sendMessage(string $phoneNumber, string $text)
    {
        try {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);

            $url = "{$this->baseUrl}/message/sendText/{$this->instanceName}";

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