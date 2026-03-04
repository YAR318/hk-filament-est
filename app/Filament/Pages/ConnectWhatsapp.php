<?php

namespace App\Filament\Pages;

use App\Services\EvolutionService;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ConnectWhatsapp extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected string $view = 'filament.pages.connect-whatsapp';

    protected static string|\UnitEnum|null $navigationGroup = 'Whatsapp';

    protected static ?string $navigationLabel = 'Conectar Whatsapp';

    protected static ?string $title = 'Conectar Whatsapp';

    protected static ?int $navigationSort = 1;

    public string $connectionState = 'loading';
    public ?string $qrCode = null;
    public ?string $profileName = null;
    public ?string $profilePic = null;
    public ?string $ownerPhone = null;

    // Formulario de creación de instancia
    public bool $showCreateForm = false;
    public bool $instanceExists = true;
    public string $newInstanceName = '';
    public string $newWebhookUrl = '';

    /**
     * Verificar si el usuario puede acceder a esta página.
     */
    public static function canAccess(): bool
    {
        return auth()->user()->can('ver_guia_whatsapp');
    }

    public function getTitle(): string|Htmlable
    {
        return 'WhatsApp - Conexión';
    }

    public function mount(): void
    {
        // Pre-cargar valores por defecto para el formulario
        $this->newWebhookUrl = 'http://n8n:5678/webhook/whatsapp-v2';
        $this->newInstanceName = config('services.evolution.instance', 'HunabkuBot');
        $this->refreshStatus();
    }

    /**
     * Refresh the connection status and instance info
     */
    public function refreshStatus(): void
    {
        try {
            $service = app(EvolutionService::class);

            // Get instance info first, as it's the most reliable way to know if it exists in v2
            $info = $service->getInstanceInfo();

            // If the instance doesn't exist (deleted), getInstanceInfo returns an empty array []
            if (empty($info) || isset($info['error'])) {
                $this->instanceExists = false;
                $this->connectionState = 'not_found';
                $this->qrCode = null;
                return;
            }

            $this->instanceExists = true;

            // Get connection state
            $stateData = $service->getConnectionState();
            $state = $stateData['instance']['state'] ?? 'unknown';

            // For v2, if fetchInstances worked but connectionState says 'close', it might be disconnected
            $this->connectionState = $state;

            $instance = $info['instance'] ?? [];
            $this->profileName = $instance['profileName'] ?? null;
            $this->profilePic = $instance['profilePictureUrl'] ?? null;
            $this->ownerPhone = $instance['owner'] ?? null;

            // Clean phone number
            if ($this->ownerPhone) {
                $this->ownerPhone = str_replace('@s.whatsapp.net', '', $this->ownerPhone);
            }

            // If disconnected, try to get QR
            if ($this->connectionState !== 'open') {
                if (empty($this->qrCode)) {
                    $this->fetchQR();
                }
            } else {
                $this->qrCode = null;
            }
        } catch (\Exception $e) {
            $this->connectionState = 'error';
            \Illuminate\Support\Facades\Log::error('Error en refreshStatus: ' . $e->getMessage());
        }
    }

    /**
     * Fetch QR code from Evolution API
     */
    public function fetchQR(): void
    {
        $service = app(EvolutionService::class);
        $qrData = $service->getConnectQR();

        // Evolution API returns base64 QR in different formats depending on version
        $this->qrCode = $qrData['base64'] ?? $qrData['qrcode'] ?? null;
    }

    /**
     * Manually request a new QR and notify the user
     */
    public function regenerateQR(): void
    {
        $this->fetchQR();

        \Filament\Notifications\Notification::make()
            ->title('QR Solicitado')
            ->body('Si el código anterior no había expirado, verás el mismo código. De lo contrario, se ha actualizado.')
            ->info()
            ->send();
    }

    /**
     * Mostrar/ocultar formulario de creación
     */
    public function toggleCreateForm(): void
    {
        $this->showCreateForm = !$this->showCreateForm;
    }

    /**
     * Crear una nueva instancia en Evolution API
     */
    public function createInstance(): void
    {
        $this->validate([
            'newInstanceName' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_-]+$/',
            'newWebhookUrl' => 'required|url',
        ], [
            'newInstanceName.required' => 'El nombre de la instancia es obligatorio.',
            'newInstanceName.regex' => 'Solo letras, números, guiones y guiones bajos.',
            'newInstanceName.min' => 'Mínimo 3 caracteres.',
            'newWebhookUrl.required' => 'La URL del webhook es obligatoria.',
            'newWebhookUrl.url' => 'Debe ser una URL válida.',
        ]);

        $service = app(EvolutionService::class);
        $result = $service->createInstance($this->newInstanceName, $this->newWebhookUrl);

        if (isset($result['error'])) {
            \Filament\Notifications\Notification::make()
                ->title('Error al crear instancia')
                ->body($result['error'])
                ->danger()
                ->send();
            return;
        }

        \Filament\Notifications\Notification::make()
            ->title('Instancia creada')
            ->body("La instancia \"{$this->newInstanceName}\" se creó correctamente. Escanea el QR para vincular.")
            ->success()
            ->send();

        // Capture the immediate QR code returned on creation
        if (isset($result['qrcode']['base64'])) {
            $this->qrCode = $result['qrcode']['base64'];
        }

        $this->showCreateForm = false;
        sleep(1);
        $this->refreshStatus();
    }

    /**
     * Configurar webhook manualmente en la instancia actual
     */
    public function configureWebhook(): void
    {
        $this->validate([
            'newWebhookUrl' => 'required|url',
        ], [
            'newWebhookUrl.required' => 'La URL del webhook es obligatoria.',
            'newWebhookUrl.url' => 'Debe ser una URL válida.',
        ]);

        $service = app(EvolutionService::class);
        $instanceName = config('services.evolution.instance', 'HunabkuBot');

        $webhookResult = $service->setWebhook($instanceName, $this->newWebhookUrl);
        $settingsResult = $service->setSettings($instanceName);

        if ($webhookResult && $settingsResult) {
            \Filament\Notifications\Notification::make()
                ->title('Configuración Completa')
                ->body("Webhook y configuraciones aplicadas correctamente en \"{$instanceName}\".")
                ->success()
                ->send();
            $this->showCreateForm = false;
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Error de configuración')
                ->body('Hubo un problema al configurar el webhook o los ajustes. Revisa los logs.')
                ->danger()
                ->send();
        }
    }

    /**
     * Eliminar la instancia actual de Evolution API
     */
    public function deleteInstance(): void
    {
        $service = app(EvolutionService::class);
        $result = $service->deleteInstance();

        if ($result) {
            \Filament\Notifications\Notification::make()
                ->title('Instancia eliminada')
                ->body('La instancia fue eliminada de Evolution API.')
                ->success()
                ->send();
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('No se pudo eliminar la instancia.')
                ->danger()
                ->send();
        }

        sleep(1);
        $this->refreshStatus();
    }

    /**
     * Disconnect the instance
     */
    public function disconnect(): void
    {
        $service = app(EvolutionService::class);
        $result = $service->disconnect();

        if ($result) {
            \Filament\Notifications\Notification::make()
                ->title('WhatsApp desconectado')
                ->body('La instancia se ha desvinculado correctamente.')
                ->success()
                ->send();
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('No se pudo desconectar la instancia.')
                ->danger()
                ->send();
        }

        // Small delay to let the state settle
        sleep(1);
        $this->refreshStatus();
    }

    /**
     * Restart the instance
     */
    public function restartInstance(): void
    {
        $service = app(EvolutionService::class);
        $result = $service->restart();

        if ($result) {
            \Filament\Notifications\Notification::make()
                ->title('Instancia reiniciada')
                ->body('La instancia se está reiniciando...')
                ->success()
                ->send();
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('No se pudo reiniciar la instancia.')
                ->danger()
                ->send();
        }

        sleep(2);
        $this->refreshStatus();
    }
}