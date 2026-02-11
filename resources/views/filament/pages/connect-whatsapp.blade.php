<x-filament-panels::page>
    <style>
        .wa-page {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Connection Card */
        .wa-status-card {
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 100%);
            position: relative;
            overflow: hidden;
        }

        .wa-status-card.disconnected {
            background: linear-gradient(135deg, #fef2f2 0%, #fff7ed 100%);
        }

        .dark .wa-status-card {
            background: linear-gradient(135deg, #064e3b22 0%, #0e374422 100%);
            border-color: #374151;
        }

        .dark .wa-status-card.disconnected {
            background: linear-gradient(135deg, #7f1d1d22 0%, #78350f22 100%);
        }

        .wa-profile-pic {
            width: 5rem;
            height: 5rem;
            border-radius: 9999px;
            margin: 0 auto 1rem;
            border: 3px solid #22c55e;
            object-fit: cover;
        }

        .wa-status-card.disconnected .wa-profile-pic {
            border-color: #ef4444;
        }

        .wa-avatar-placeholder {
            width: 5rem;
            height: 5rem;
            border-radius: 9999px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: linear-gradient(135deg, #22c55e, #10b981);
            color: white;
        }

        .wa-status-card.disconnected .wa-avatar-placeholder {
            background: linear-gradient(135deg, #ef4444, #f97316);
        }

        .wa-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .wa-status-badge.connected {
            background-color: #dcfce7;
            color: #166534;
        }

        .wa-status-badge.disconnected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .dark .wa-status-badge.connected {
            background-color: #166534;
            color: #dcfce7;
        }

        .dark .wa-status-badge.disconnected {
            background-color: #991b1b;
            color: #fee2e2;
        }

        .wa-status-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            display: inline-block;
        }

        .wa-status-dot.connected {
            background-color: #22c55e;
        }

        .wa-status-dot.disconnected {
            background-color: #ef4444;
        }

        .wa-phone {
            font-size: 0.85rem;
            opacity: 0.6;
            margin-top: 0.25rem;
        }

        /* QR Section */
        .wa-qr-section {
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            border: 2px dashed #3b82f6;
            text-align: center;
            background-color: white;
        }

        .dark .wa-qr-section {
            background-color: #1f2937;
            border-color: #60a5fa;
        }

        .wa-qr-image {
            max-width: 280px;
            margin: 1rem auto;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Steps */
        .wa-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .wa-step {
            border-radius: 0.75rem;
            padding: 1.25rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            text-align: center;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .wa-step:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .dark .wa-step {
            background-color: #1f2937;
            border-color: #374151;
        }

        .wa-step-number {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.95rem;
            color: white;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            margin-bottom: 0.75rem;
        }

        .wa-step h4 {
            font-weight: 700;
            font-size: 0.9rem;
            margin: 0 0 0.35rem;
        }

        .wa-step p {
            font-size: 0.78rem;
            opacity: 0.65;
            margin: 0;
            line-height: 1.4;
        }

        /* Action Buttons */
        .wa-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }

        .wa-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1.2rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .wa-btn:hover {
            filter: brightness(0.9);
        }

        .wa-btn-green {
            background-color: #22c55e;
            color: white;
        }

        .wa-btn-blue {
            background-color: #3b82f6;
            color: white;
        }

        .wa-btn-red {
            background-color: #ef4444;
            color: white;
        }

        .wa-btn-gray {
            background-color: #6b7280;
            color: white;
        }

        /* Info Section */
        .wa-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 640px) {
            .wa-info-grid {
                grid-template-columns: 1fr;
            }
        }

        .wa-info-card {
            border-radius: 0.75rem;
            padding: 1.25rem;
            border: 1px solid #e5e7eb;
            background-color: white;
        }

        .dark .wa-info-card {
            background-color: #1f2937;
            border-color: #374151;
        }

        .wa-info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 0.4rem;
        }

        .wa-info-value {
            font-size: 0.85rem;
            font-family: ui-monospace, monospace;
            word-break: break-all;
            padding: 0.4rem 0.6rem;
            background-color: #f3f4f6;
            border-radius: 0.35rem;
            cursor: pointer;
            position: relative;
        }

        .dark .wa-info-value {
            background-color: #111827;
        }

        .wa-info-value:hover::after {
            content: 'Copiar';
            position: absolute;
            right: 0.4rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.65rem;
            color: #9ca3af;
            font-family: sans-serif;
        }

        /* Spinner */
        .wa-spinner {
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid #e5e7eb;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="wa-page">

        {{-- STATUS CARD --}}
        <div class="wa-status-card {{ $connectionState !== 'open' ? 'disconnected' : '' }}"
            wire:poll.5s="refreshStatus">
            @if($connectionState === 'open')
            {{-- Connected --}}
            @if($profilePic)
            <img src="{{ $profilePic }}" class="wa-profile-pic" alt="Profile">
            @else
            <div class="wa-avatar-placeholder">📱</div>
            @endif

            <h2 style="font-size: 1.3rem; font-weight: 800; margin: 0 0 0.25rem;">
                {{ $profileName ?? 'WhatsApp Conectado' }}
            </h2>
            @if($ownerPhone)
            <p class="wa-phone">+{{ $ownerPhone }}</p>
            @endif

            <div style="margin-top: 0.75rem;">
                <span class="wa-status-badge connected">
                    <span class="wa-status-dot connected"></span>
                    Conectado
                </span>
            </div>

            <div class="wa-actions">
                <x-filament::button size="sm" color="info" wire:click="refreshStatus">
                    🔄 Actualizar
                </x-filament::button>
                <x-filament::button size="sm" color="gray" wire:click="restartInstance"
                    x-on:click="if(!confirm('¿Reiniciar la instancia de WhatsApp?')) $event.stopImmediatePropagation()">
                    ⚡ Reiniciar
                </x-filament::button>
                <x-filament::button size="sm" color="danger" wire:click="disconnect"
                    x-on:click="if(!confirm('¿Desconectar WhatsApp? Tendrás que escanear el QR de nuevo.')) $event.stopImmediatePropagation()">
                    🔌 Desconectar
                </x-filament::button>
            </div>

            @elseif($connectionState === 'loading')
            {{-- Loading --}}
            <div class="wa-spinner" style="margin: 0 auto 1rem;"></div>
            <h2 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Verificando conexión...</h2>

            @else
            {{-- Disconnected --}}
            <div class="wa-avatar-placeholder">📵</div>
            <h2 style="font-size: 1.3rem; font-weight: 800; margin: 0 0 0.25rem;">
                WhatsApp Desconectado
            </h2>
            <p style="font-size: 0.85rem; opacity: 0.6; margin: 0;">
                Escanea el código QR para conectar tu número
            </p>
            <div style="margin-top: 0.75rem;">
                <span class="wa-status-badge disconnected">
                    <span class="wa-status-dot disconnected"></span>
                    Desconectado
                </span>
            </div>
            @endif
        </div>

        {{-- QR CODE (only if disconnected) --}}
        @if($connectionState !== 'open' && $connectionState !== 'loading')
        <div class="wa-qr-section" wire:poll.10s="fetchQR">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 0.5rem;">
                📷 Escanea el código QR
            </h3>
            <p style="font-size: 0.8rem; opacity: 0.6; margin: 0 0 1rem;">
                Abre WhatsApp en tu celular → Ajustes → Dispositivos vinculados → Vincular dispositivo
            </p>

            @if($qrCode)
            <img src="{{ $qrCode }}" class="wa-qr-image" alt="QR Code">
            <p style="font-size: 0.7rem; opacity: 0.5; margin-top: 0.75rem;">
                El QR se actualiza automáticamente cada 10 segundos. Si expira, presiona el botón.
            </p>
            @else
            <div style="padding: 2rem; opacity: 0.5;">
                <div class="wa-spinner" style="margin: 0 auto 1rem;"></div>
                <p style="font-size: 0.85rem;">Obteniendo código QR...</p>
            </div>
            @endif

            <div class="wa-actions" style="margin-top: 1rem;">
                <x-filament::button size="sm" color="info" wire:click="fetchQR">
                    🔄 Regenerar QR
                </x-filament::button>
            </div>
        </div>
        @endif

        {{-- STEPS --}}
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem;">
            📋 ¿Cómo conectar?
        </h3>
        <div class="wa-steps">
            <div class="wa-step">
                <div class="wa-step-number">1</div>
                <h4>Abre WhatsApp</h4>
                <p>En tu celular, abre la app de WhatsApp</p>
            </div>
            <div class="wa-step">
                <div class="wa-step-number">2</div>
                <h4>Dispositivos vinculados</h4>
                <p>Ve a Ajustes → Dispositivos vinculados → Vincular dispositivo</p>
            </div>
            <div class="wa-step">
                <div class="wa-step-number">3</div>
                <h4>Escanea el QR</h4>
                <p>Apunta la cámara al código QR que aparece arriba</p>
            </div>
            <div class="wa-step">
                <div class="wa-step-number">4</div>
                <h4>¡Listo!</h4>
                <p>El estado cambiará a "Conectado" automáticamente</p>
            </div>
        </div>

        {{-- TECHNICAL INFO --}}
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem;">
            ⚙️ Información técnica
        </h3>
        <div class="wa-info-grid">
            <div class="wa-info-card">
                <p class="wa-info-label">Webhook URL</p>
                <div class="wa-info-value" onclick="navigator.clipboard.writeText(this.innerText.trim())">
                    {{ config('app.url') }}/api/whatsapp/webhook
                </div>
                <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.3rem;">
                    Configura esta URL en Evolution API para recibir mensajes.
                </p>
            </div>
            <div class="wa-info-card">
                <p class="wa-info-label">Instancia</p>
                <div class="wa-info-value" onclick="navigator.clipboard.writeText(this.innerText.trim())">
                    {{ config('services.evolution.instance', 'HunabkuBot') }}
                </div>
                <p style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.3rem;">
                    Nombre de la instancia configurada en Evolution API.
                </p>
            </div>
            <div class="wa-info-card">
                <p class="wa-info-label">Eventos requeridos</p>
                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.3rem;">
                    <span
                        style="padding: 0.2rem 0.5rem; background: #dbeafe; color: #1e40af; border-radius: 0.3rem; font-size: 0.75rem; font-weight: 600;">MESSAGES_UPSERT</span>
                    <span
                        style="padding: 0.2rem 0.5rem; background: #dbeafe; color: #1e40af; border-radius: 0.3rem; font-size: 0.75rem; font-weight: 600;">MESSAGES_UPDATE</span>
                    <span
                        style="padding: 0.2rem 0.5rem; background: #dbeafe; color: #1e40af; border-radius: 0.3rem; font-size: 0.75rem; font-weight: 600;">SEND_MESSAGE</span>
                </div>
            </div>
            <div class="wa-info-card">
                <p class="wa-info-label">¿Necesitas ayuda?</p>
                <p style="font-size: 0.8rem; opacity: 0.7; margin: 0.3rem 0;">
                    Contacta al administrador del sistema si tienes problemas.
                </p>
                <a href="https://docs.evolution-api.com/" target="_blank"
                    style="font-size: 0.8rem; color: #3b82f6; text-decoration: none; font-weight: 600;">
                    📖 Documentación oficial →
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>