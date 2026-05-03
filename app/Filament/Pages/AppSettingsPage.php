<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use App\Services\EvolutionService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class AppSettingsPage extends Page
{
    protected static ?string $navigationLabel = 'Configuracion';
    protected static ?string $title = 'Configuracion del Sistema';

    protected string $view = 'filament.pages.app-settings';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion';
    }

    public static function getNavigationSort(): ?int
    {
        return 50;
    }

    // Propiedades del formulario - General
    public string $admin_email = '';
    public string $work_start_hour = '9';
    public string $work_end_hour = '17';
    public string $work_days = '1,2,3,4,5';

    // Propiedades del formulario - WhatsApp
    public string $whatsapp_provider = 'evolution';
    public string $meta_phone_id = '';
    public string $meta_access_token = '';
    public string $meta_verify_token = '';

    // Propiedades de Evolution API
    public string $connectionState = 'loading';
    public ?string $qrCode = null;
    public ?string $profileName = null;
    public ?string $profilePic = null;
    public ?string $ownerPhone = null;

    public bool $showCreateForm = false;
    public bool $instanceExists = true;
    public string $newInstanceName = '';
    public string $newWebhookUrl = '';

    public function mount(): void
    {
        $this->admin_email = AppSetting::get('admin_email', '') ?? '';
        $this->work_start_hour = AppSetting::get('work_start_hour', '9') ?? '9';
        $this->work_end_hour = AppSetting::get('work_end_hour', '17') ?? '17';
        $this->work_days = AppSetting::get('work_days', '1,2,3,4,5') ?? '1,2,3,4,5';

        $this->whatsapp_provider = AppSetting::get('whatsapp_provider', 'evolution') ?? 'evolution';
        $this->meta_phone_id = AppSetting::get('meta_phone_id', '') ?? '';
        $this->meta_access_token = AppSetting::get('meta_access_token', '') ?? '';
        $this->meta_verify_token = AppSetting::get('meta_verify_token', '') ?? '';

        // Init Evolution API
        $this->newWebhookUrl = 'http://n8n:5678/webhook/whatsapp-v2';
        $this->newInstanceName = config('services.evolution.instance', 'HunabkuBot');
        
        if ($this->whatsapp_provider === 'evolution') {
            $this->refreshStatus();
        }
    }

    /**
     * Guardar configuracion general
     */
    public function saveSettings(): void
    {
        $this->validate([
            'admin_email' => 'required|email',
            'work_start_hour' => 'required|integer|min:0|max:23',
            'work_end_hour' => 'required|integer|min:1|max:24|gt:work_start_hour',
            'work_days' => 'required|string',
        ]);

        AppSetting::set('admin_email', $this->admin_email);
        AppSetting::set('work_start_hour', $this->work_start_hour);
        AppSetting::set('work_end_hour', $this->work_end_hour);
        AppSetting::set('work_days', $this->work_days);

        Notification::make()
            ->title('Configuracion guardada')
            ->body('Los cambios se aplicaran de inmediato.')
            ->success()
            ->send();
    }

    /**
     * Guardar configuracion de WhatsApp
     */
    public function saveWhatsAppSettings(): void
    {
        $rules = [
            'whatsapp_provider' => 'required|in:evolution,meta',
        ];

        // Validar credenciales de Meta solo si se selecciona Meta
        if ($this->whatsapp_provider === 'meta') {
            $rules['meta_phone_id'] = 'required|string';
            $rules['meta_access_token'] = 'required|string';
            $rules['meta_verify_token'] = 'required|string';
        }

        $this->validate($rules);

        AppSetting::set('whatsapp_provider', $this->whatsapp_provider);
        AppSetting::set('meta_phone_id', $this->meta_phone_id);
        AppSetting::set('meta_access_token', $this->meta_access_token);
        AppSetting::set('meta_verify_token', $this->meta_verify_token);

        Notification::make()
            ->title('Proveedor WhatsApp actualizado')
            ->body("Proveedor activo: " . strtoupper($this->whatsapp_provider))
            ->success()
            ->send();

        if ($this->whatsapp_provider === 'evolution') {
            $this->refreshStatus();
        }
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
        $service = app(EvolutionService::class);
        
        $service->disconnect();
        sleep(2);

        $this->fetchQR();

        Notification::make()
            ->title('QR Actualizado')
            ->body('Si tu código estaba expirado, ahora se ha generado uno nuevo listo para escanear.')
            ->success()
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
            Notification::make()
                ->title('Error al crear instancia')
                ->body($result['error'])
                ->danger()
                ->send();
            return;
        }

        Notification::make()
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
            Notification::make()
                ->title('Configuración Completa')
                ->body("Webhook y configuraciones aplicadas correctamente en \"{$instanceName}\".")
                ->success()
                ->send();
            $this->showCreateForm = false;
        } else {
            Notification::make()
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
            Notification::make()
                ->title('Instancia eliminada')
                ->body('La instancia fue eliminada de Evolution API.')
                ->success()
                ->send();
        } else {
            Notification::make()
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
            Notification::make()
                ->title('WhatsApp desconectado')
                ->body('La instancia se ha desvinculado correctamente.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Error')
                ->body('No se pudo desconectar la instancia.')
                ->danger()
                ->send();
        }

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
            Notification::make()
                ->title('Instancia reiniciada')
                ->body('La instancia se está reiniciando...')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Error')
                ->body('No se pudo reiniciar la instancia.')
                ->danger()
                ->send();
        }

        sleep(2);
        $this->refreshStatus();
    }
}