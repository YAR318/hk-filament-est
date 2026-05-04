<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppProviderInterface;
use App\Services\EvolutionService;
use Illuminate\Support\Facades\Log;

/**
 * Proveedor WhatsApp usando Evolution API (no oficial).
 * Wrapper del EvolutionService existente.
 */
class EvolutionWhatsAppProvider implements WhatsAppProviderInterface
{
    protected EvolutionService $evolutionService;

    public function __construct(EvolutionService $evolutionService)
    {
        $this->evolutionService = $evolutionService;
    }

    public function sendMessage(string $phoneNumber, string $text): array
    {
        try {
            $result = $this->evolutionService->sendMessage($phoneNumber, $text);
            return [
                'success' => true,
                'provider' => 'evolution',
                'data' => $result,
            ];
        }
        catch (\Exception $e) {
            Log::warning('EvolutionWhatsAppProvider: Error enviando mensaje', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'provider' => 'evolution',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getProviderName(): string
    {
        return 'evolution';
    }

    public function isConfigured(): bool
    {
        $baseUrl = config('services.evolution.base_url');
        $apiKey = config('services.evolution.api_key');
        return !empty($baseUrl) && !empty($apiKey);
    }
}