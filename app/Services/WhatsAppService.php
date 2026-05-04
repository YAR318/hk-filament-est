<?php

namespace App\Services;

use App\Contracts\WhatsAppProviderInterface;
use App\Models\AppSetting;
use App\Services\WhatsApp\EvolutionWhatsAppProvider;
use App\Services\WhatsApp\MetaWhatsAppProvider;
use Illuminate\Support\Facades\Log;

/**
 * Fachada unificada de WhatsApp.
 * Lee la configuración del proveedor activo y delega al proveedor correcto.
 * Soporta envío por canal específico (para responder por donde llegó el mensaje).
 */
class WhatsAppService
{
    protected ?WhatsAppProviderInterface $evolutionProvider = null;
    protected ?WhatsAppProviderInterface $metaProvider = null;

    public function __construct(
        EvolutionWhatsAppProvider $evolutionProvider,
        MetaWhatsAppProvider $metaProvider
        )
    {
        $this->evolutionProvider = $evolutionProvider;
        $this->metaProvider = $metaProvider;
    }

    /**
     * Enviar mensaje usando el proveedor por defecto configurado
     */
    public function sendMessage(string $phoneNumber, string $text): array
    {
        $provider = $this->getDefaultProvider();
        return $provider->sendMessage($phoneNumber, $text);
    }

    /**
     * Enviar mensaje por un canal específico
     * Útil para responder por el mismo canal por donde llegó el mensaje
     */
    public function sendMessageVia(string $channel, string $phoneNumber, string $text): array
    {
        $provider = $this->getProvider($channel);
        return $provider->sendMessage($phoneNumber, $text);
    }

    /**
     * Obtener el proveedor por defecto configurado
     */
    public function getDefaultProvider(): WhatsAppProviderInterface
    {
        $providerName = AppSetting::get('whatsapp_provider', 'evolution') ?? 'evolution';
        return $this->getProvider($providerName);
    }

    /**
     * Obtener un proveedor específico por nombre
     */
    public function getProvider(string $name): WhatsAppProviderInterface
    {
        return match ($name) {
                'meta' => $this->metaProvider,
                default => $this->evolutionProvider,
            };
    }

    /**
     * Obtener el nombre del proveedor activo
     */
    public function getProviderName(): string
    {
        return $this->getDefaultProvider()->getProviderName();
    }

    /**
     * Verificar qué proveedores están configurados
     */
    public function getConfiguredProviders(): array
    {
        $providers = [];
        if ($this->evolutionProvider->isConfigured()) {
            $providers[] = 'evolution';
        }
        if ($this->metaProvider->isConfigured()) {
            $providers[] = 'meta';
        }
        return $providers;
    }
}