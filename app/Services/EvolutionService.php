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
                $data = $response->json();
                // Handle v2 format {"instance": {"state": "connecting"}} or {"instance": {"connectionStatus": "connecting"}}
                if (isset($data['instance']['state'])) {
                    return $data;
                }
                if (isset($data['instance']['connectionStatus'])) {
                    $data['instance']['state'] = $data['instance']['connectionStatus'];
                    return $data;
                }
                return $data;
            }

            return ['instance' => ['state' => 'unknown']];
        } catch (\Exception $e) {
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
            // In v2, we trigger the connection to get the base64 QR
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get("{$this->baseUrl}/instance/connect/{$this->instanceName}");

            if ($response->successful()) {
                $data = $response->json();

                // If it returned {"count": 0}, we need to fetch the base64 directly or from creation
                // But normally it should return {"base64": "..."} or {"code": "...", "base64": "..."}
                if (isset($data['base64'])) {
                    return ['base64' => $data['base64']];
                }

                return $data;
            }

            return ['error' => 'No se pudo obtener el QR', 'status' => $response->status()];
        } catch (\Exception $e) {
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
            // In v2, fetchInstances returns an array with 'name' and 'connectionStatus' instead of 'instanceName' and 'state'
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

                // If it's an array of instances, get the first one that matches our name
                if (isset($data[0])) {
                    $instance = null;
                    foreach ($data as $inst) {
                        $name = $inst['name'] ?? $inst['instance']['instanceName'] ?? '';
                        if (strtolower($name) === strtolower($this->instanceName)) {
                            $instance = $inst;
                            break;
                        }
                    }
                    if (!$instance)
                        $instance = $data[0];

                    // Normalize fields for the rest of the app
                    if (isset($instance['connectionStatus']) && !isset($instance['instance']['state'])) {
                        $instance['instance'] = [
                            'instanceName' => $instance['name'],
                            'state' => $instance['connectionStatus'],
                            'owner' => $instance['ownerJid'] ?? null,
                            'profileName' => $instance['profileName'] ?? null,
                        ];
                    }
                    return $instance;
                }

                // If it has 'instance' key directly (v1 single response)
                if (isset($data['instance'])) {
                    return $data;
                }

                return $data;
            }

            return [];
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
                        'text' => $text
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

        } catch (\Exception $e) {
            Log::error('Excepción al enviar mensaje por Evolution API: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear una nueva instancia en Evolution API
     */
    public function createInstance(string $instanceName, string $webhookUrl): array
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/instance/create", [
                        'instanceName' => $instanceName,
                        'integration' => 'WHATSAPP-BAILEYS',
                        'qrcode' => true,
                        'webhook' => [
                            'url' => $webhookUrl,
                            'webhookByEvents' => false,
                            'webhookBase64' => false,
                            'events' => [
                                'MESSAGES_UPSERT',
                                'CONNECTION_UPDATE',
                                'SEND_MESSAGE'
                            ]
                        ],
                        'settings' => [
                            'rejectCall' => false,
                            'msgCall' => '',
                            'groupsIgnore' => true,
                            'alwaysOnline' => false,
                            'readMessages' => false,
                            'readStatus' => false,
                            'syncFullHistory' => false
                        ]
                    ]);

            if ($response->successful()) {
                Log::info('Instancia creada en Evolution API', [
                    'instance' => $instanceName,
                ]);

                return $response->json();
            }

            if ($response->status() === 403 && str_contains($response->body(), 'already in use')) {
                // Si la instancia ya existe (fue reciclada en v2), lo tratamos como éxito
                Log::info('Instancia reciclada en Evolution API', ['instance' => $instanceName]);

                return ['instance' => ['instanceName' => $instanceName], 'recycled' => true];
            }

            Log::error('Error al crear instancia', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['error' => 'Error al crear instancia: ' . $response->body()];
        } catch (\Exception $e) {
            Log::error('Excepción al crear instancia: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Configurar webhook de una instancia en Evolution API
     */
    public function setWebhook(string $instanceName, string $webhookUrl): bool
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/webhook/set/{$instanceName}", [
                        'webhook' => [
                            'enabled' => true,
                            'url' => $webhookUrl,
                            'webhookByEvents' => false,
                            'webhookBase64' => false,
                            'events' => [
                                'MESSAGES_UPSERT',
                            ],
                        ]
                    ]);

            if ($response->successful()) {
                Log::info('Webhook configurado para instancia', [
                    'instance' => $instanceName,
                    'url' => $webhookUrl,
                ]);
                return true;
            }

            Log::error('Error al configurar webhook', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Excepción al configurar webhook: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Configurar opciones de la instancia (ignorar grupos, rechazar llamadas, etc)
     */
    public function setSettings(string $instanceName): bool
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/settings/set/{$instanceName}", [
                        'rejectCall' => false,
                        'msgCall' => '',
                        'groupsIgnore' => true,
                        'alwaysOnline' => false,
                        'readMessages' => false,
                        'readStatus' => false,
                        'syncFullHistory' => false
                    ]);

            if ($response->successful()) {
                Log::info('Settings configurados para instancia', ['instance' => $instanceName]);
                return true;
            }

            Log::error('Error al configurar settings', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Excepción al configurar settings: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar una instancia de Evolution API
     */
    public function deleteInstance(?string $instanceName = null): bool
    {
        $name = $instanceName ?? $this->instanceName;

        try {
            // In v2, we often need to logout first before deleting, otherwise it might fail
            try {
                Http::timeout(10)->withHeaders([
                    'apikey' => $this->apiKey,
                ])->delete("{$this->baseUrl}/instance/logout/{$name}");
            } catch (\Exception $e) {
                // Ignore logout errors, just proceed to delete
            }

            $response = Http::timeout(10)->withHeaders([
                'apikey' => $this->apiKey,
            ])->delete("{$this->baseUrl}/instance/delete/{$name}");

            if ($response->successful() || $response->status() === 404) {
                Log::info('Instancia eliminada vía API (o ya no existía)', ['instance' => $name]);
                return true;
            }

            Log::error('Error al eliminar instancia', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Excepción al eliminar instancia: ' . $e->getMessage());
            return false;
        }
    }
}