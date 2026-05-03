<x-filament-panels::page>
    <div style="max-width: 640px;">
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

        /* Create Instance Form */
        .wa-create-form {
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            border: 2px solid #8b5cf6;
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        }

        .dark .wa-create-form {
            background: linear-gradient(135deg, #2e1065aa 0%, #1e1b4baa 100%);
            border-color: #7c3aed;
        }

        .wa-form-group {
            margin-bottom: 1rem;
        }

        .wa-form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .wa-form-group input {
            width: 100%;
            padding: 0.6rem 0.9rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            background: white;
            transition: border-color 0.15s;
        }

        .wa-form-group input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        }

        .dark .wa-form-group input {
            background: #1f2937;
            border-color: #4b5563;
            color: #e5e7eb;
        }

        .wa-form-group .wa-form-hint {
            font-size: 0.72rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }

        .wa-form-group .wa-form-error {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.25rem;
        }

        .wa-status-card.not-found {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            border-color: #8b5cf6;
        }

        .dark .wa-status-card.not-found {
            background: linear-gradient(135deg, #2e1065aa 0%, #1e1b4baa 100%);
            border-color: #7c3aed;
        }
    
            .settings-card {
                background: var(--gray-50);
                border: 1px solid var(--gray-200);
                border-radius: 12px;
                padding: 24px;
                margin-bottom: 20px;
            }

            .dark .settings-card {
                background: var(--gray-800);
                border-color: var(--gray-700);
            }

            .settings-card h3 {
                font-size: 1.05rem;
                font-weight: 700;
                margin: 0 0 4px;
            }

            .settings-card p.desc {
                font-size: 0.82rem;
                opacity: 0.6;
                margin: 0 0 16px;
            }

            .form-group {
                margin-bottom: 14px;
            }

            .form-group label {
                display: block;
                font-size: 0.85rem;
                font-weight: 600;
                margin-bottom: 6px;
            }

            .form-group input,
            .form-group select {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid var(--gray-300);
                border-radius: 8px;
                font-size: 0.9rem;
                background: white;
                transition: border-color 0.2s;
            }

            .dark .form-group input,
            .dark .form-group select {
                background: var(--gray-900);
                border-color: var(--gray-600);
                color: white;
            }

            .form-group input:focus,
            .form-group select:focus {
                outline: none;
                border-color: #6366f1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            }

            .form-hint {
                font-size: 0.78rem;
                opacity: 0.5;
                margin: 4px 0 0;
            }

            .form-error {
                color: #ef4444;
                font-size: 0.78rem;
                margin: 4px 0 0;
            }

            .days-grid {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            .day-chip {
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 0.82rem;
                font-weight: 600;
                cursor: pointer;
                border: 2px solid var(--gray-300);
                background: white;
                transition: all 0.2s;
                user-select: none;
            }

            .dark .day-chip {
                background: var(--gray-900);
                border-color: var(--gray-600);
                color: white;
            }

            .day-chip.active {
                background: #6366f1;
                border-color: #6366f1;
                color: white;
            }

            .hours-row {
                display: flex;
                gap: 12px;
                align-items: center;
            }

            .hours-row .form-group {
                flex: 1;
            }

            .hours-separator {
                font-weight: 700;
                font-size: 1.1rem;
                padding-top: 22px;
            }

            .provider-selector {
                display: flex;
                gap: 12px;
                margin-bottom: 16px;
            }

            .provider-option {
                flex: 1;
                padding: 16px;
                border-radius: 10px;
                border: 2px solid var(--gray-300);
                cursor: pointer;
                text-align: center;
                transition: all 0.2s;
                user-select: none;
            }

            .dark .provider-option {
                border-color: var(--gray-600);
            }

            .provider-option.selected {
                border-color: #25D366;
                background: rgba(37, 211, 102, 0.08);
            }

            .provider-option .provider-icon {
                font-size: 1.6rem;
                margin-bottom: 6px;
            }

            .provider-option .provider-name {
                font-weight: 700;
                font-size: 0.9rem;
            }

            .provider-option .provider-desc {
                font-size: 0.75rem;
                opacity: 0.5;
                margin-top: 2px;
            }

            .meta-fields {
                padding-top: 8px;
                border-top: 1px solid var(--gray-200);
                margin-top: 12px;
            }

            .dark .meta-fields {
                border-color: var(--gray-700);
            }

            .webhook-url {
                background: var(--gray-100);
                padding: 10px 14px;
                border-radius: 8px;
                font-family: monospace;
                font-size: 0.82rem;
                word-break: break-all;
                margin-top: 6px;
            }

            .dark .webhook-url {
                background: var(--gray-900);
            }
        </style>

        {{-- Correo del encargado --}}
        <div class="settings-card">
            <h3>Correo del Encargado</h3>
            <p class="desc">Este correo recibira las notificaciones de citas (confirmacion, cancelacion, recordatorios).
            </p>

            <div class="form-group">
                <label for="admin_email">Correo electronico</label>
                <input type="email" id="admin_email" wire:model="admin_email" placeholder="tucorreo@gmail.com">
                <p class="form-hint">Puedes cambiarlo cuando quieras</p>
                @error('admin_email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Horario laboral --}}
        <div class="settings-card">
            <h3>Horario Laboral</h3>
            <p class="desc">Las citas solo se podran agendar dentro de este horario.</p>

            <div class="hours-row">
                <div class="form-group">
                    <label for="work_start_hour">Hora de inicio</label>
                    <select id="work_start_hour" wire:model="work_start_hour">
                        @for($h = 6; $h <= 20; $h++) <option value="{{ $h }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00
                            </option>
                            @endfor
                    </select>
                </div>
                <span class="hours-separator">&rarr;</span>
                <div class="form-group">
                    <label for="work_end_hour">Hora de fin</label>
                    <select id="work_end_hour" wire:model="work_end_hour">
                        @for($h = 7; $h <= 22; $h++) <option value="{{ $h }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00
                            </option>
                            @endfor
                    </select>
                </div>
            </div>
            @error('work_start_hour') <p class="form-error">{{ $message }}</p> @enderror
            @error('work_end_hour') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        {{-- Dias laborales --}}
        <div class="settings-card">
            <h3>Dias Laborales</h3>
            <p class="desc">Selecciona los dias en que se pueden agendar citas.</p>

            <div class="form-group">
                <input type="hidden" wire:model="work_days">
                @php
                $dayNames = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];
                $selectedDays = explode(',', $work_days);
                @endphp
                <div class="days-grid">
                    @foreach($dayNames as $index => $dayName)
                    @php $dayNum = $index + 1; @endphp
                    <div class="day-chip {{ in_array((string)$dayNum, $selectedDays) ? 'active' : '' }}" x-data
                        x-on:click="
                                let days = $wire.work_days.split(',').filter(d => d !== '');
                                let idx = days.indexOf('{{ $dayNum }}');
                                if (idx > -1) { days.splice(idx, 1); } else { days.push('{{ $dayNum }}'); }
                                days.sort();
                                $wire.work_days = days.join(',');
                                $el.classList.toggle('active');
                            ">
                        {{ $dayName }}
                    </div>
                    @endforeach
                </div>
                @error('work_days') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Boton guardar general --}}
        <div style="text-align: right; margin-bottom: 32px;">
            <x-filament::button wire:click="saveSettings" color="success" size="lg">
                Guardar Configuracion
            </x-filament::button>
        </div>

        {{-- Separador --}}
        <hr style="border: none; border-top: 2px solid var(--gray-200); margin: 32px 0;">

        {{-- Proveedor WhatsApp --}}
        <div class="settings-card">
            <h3>Proveedor WhatsApp</h3>
            <p class="desc">Selecciona el proveedor para enviar y recibir mensajes de WhatsApp.</p>

            <div class="provider-selector" x-data>
                <div class="provider-option {{ $whatsapp_provider === 'evolution' ? 'selected' : '' }}"
                    x-on:click="$wire.whatsapp_provider = 'evolution'; document.querySelectorAll('.provider-option').forEach(el => el.classList.remove('selected')); $el.classList.add('selected');">
                    <div class="provider-icon">E</div>
                    <div class="provider-name">Evolution API</div>
                    <div class="provider-desc">No oficial - WhatsApp personal</div>
                </div>
                <div class="provider-option {{ $whatsapp_provider === 'meta' ? 'selected' : '' }}"
                    x-on:click="$wire.whatsapp_provider = 'meta'; document.querySelectorAll('.provider-option').forEach(el => el.classList.remove('selected')); $el.classList.add('selected');">
                    <div class="provider-icon">M</div>
                    <div class="provider-name">Meta Cloud API</div>
                    <div class="provider-desc">Oficial - WhatsApp Business</div>
                </div>
            </div>
            @error('whatsapp_provider') <p class="form-error">{{ $message }}</p> @enderror

            {{-- Campos de Meta API (solo visibles si se selecciona Meta) --}}
            <div x-data="{ show: @entangle('whatsapp_provider') }" x-show="show === 'meta'" x-transition
                class="meta-fields">

                <div class="form-group">
                    <label for="meta_phone_id">Phone Number ID</label>
                    <input type="text" id="meta_phone_id" wire:model="meta_phone_id" placeholder="123456789012345">
                    <p class="form-hint">Lo encuentras en Meta Business Suite &rarr; WhatsApp &rarr; API Setup</p>
                    @error('meta_phone_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="meta_access_token">Access Token (permanente)</label>
                    <input type="password" id="meta_access_token" wire:model="meta_access_token" placeholder="EAAG...">
                    <p class="form-hint">Token permanente generado en tu App de Facebook</p>
                    @error('meta_access_token') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="meta_verify_token">Verify Token</label>
                    <input type="text" id="meta_verify_token" wire:model="meta_verify_token"
                        placeholder="mi_token_secreto_123">
                    <p class="form-hint">Token que usas para verificar el webhook en Meta</p>
                    @error('meta_verify_token') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>URL del Webhook</label>
                    <div class="webhook-url">
                        {{ url('/api/webhook/meta') }}
                    </div>
                    <p class="form-hint">Copia esta URL en la configuracion del webhook de Meta</p>
                </div>
            </div>

            {{-- Info de Evolution --}}
            <div x-data="{ show: @entangle('whatsapp_provider') }" x-show="show === 'evolution'" x-transition class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="wa-page" style="max-width: 100%; margin: 0;">


        {{-- STATUS CARD --}}
        <div class="wa-status-card {{ $connectionState === 'not_found' ? 'not-found' : ($connectionState !== 'open' ? 'disconnected' : '') }}"
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
                    Actualizar
                </x-filament::button>
                <x-filament::button size="sm" color="warning" wire:click="toggleCreateForm">
                    ⚙️ Configurar Webhook
                </x-filament::button>
                <x-filament::button size="sm" color="gray" wire:click="restartInstance"
                    x-on:click="if(!confirm('¿Reiniciar la instancia de WhatsApp?')) $event.stopImmediatePropagation()">
                    Reiniciar
                </x-filament::button>
                <x-filament::button size="sm" color="danger" wire:click="disconnect"
                    x-on:click="if(!confirm('¿Desconectar WhatsApp? Tendrás que escanear el QR de nuevo.')) $event.stopImmediatePropagation()">
                    Desconectar
                </x-filament::button>
                <x-filament::button size="sm" color="danger" wire:click="deleteInstance"
                    x-on:click="if(!confirm('⚠️ ¿Eliminar la instancia completa? Esta acción no se puede deshacer. Tendrás que crear una nueva.')) $event.stopImmediatePropagation()">
                    Eliminar Instancia
                </x-filament::button>
            </div>

            @elseif($connectionState === 'not_found')
            {{-- Instance Not Found --}}
            <div class="wa-avatar-placeholder" style="background: linear-gradient(135deg, #8b5cf6, #6366f1);">🔧</div>
            <h2 style="font-size: 1.3rem; font-weight: 800; margin: 0 0 0.25rem;">
                Instancia no encontrada
            </h2>
            <p style="font-size: 0.85rem; opacity: 0.6; margin: 0;">
                No existe una instancia configurada en Evolution API. Crea una nueva para comenzar.
            </p>
            <div class="wa-actions" style="margin-top: 1rem;">
                <x-filament::button size="sm" color="info" wire:click="toggleCreateForm">
                    ➕ Crear Instancia
                </x-filament::button>
                <x-filament::button size="sm" color="gray" wire:click="refreshStatus">
                    Reintentar
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
            <div class="wa-actions" style="margin-top: 0.75rem;">
                <x-filament::button size="sm" color="warning" wire:click="toggleCreateForm">
                    ⚙️ Configurar Webhook
                </x-filament::button>
                <x-filament::button size="sm" color="danger" wire:click="deleteInstance"
                    x-on:click="if(!confirm('⚠️ ¿Eliminar la instancia completa? Tendrás que crear una nueva.')) $event.stopImmediatePropagation()">
                    Eliminar Instancia
                </x-filament::button>
            </div>
            @endif
        </div>

        {{-- CREATE INSTANCE / CONFIGURE WEBHOOK FORM --}}
        @if($showCreateForm)
        <div class="wa-create-form">
            @if(!$instanceExists)
            {{-- Modo: Crear instancia nueva --}}
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1rem;">
                🔧 Crear nueva instancia
            </h3>

            <div class="wa-form-group">
                <label for="newInstanceName">Nombre de la instancia</label>
                <input type="text" id="newInstanceName" wire:model="newInstanceName" placeholder="HunabkuBot">
                <p class="wa-form-hint">Solo letras, números, guiones y guiones bajos (ej: HunabkuBot)</p>
                @error('newInstanceName') <p class="wa-form-error">{{ $message }}</p> @enderror
            </div>

            <div class="wa-form-group">
                <label for="newWebhookUrl">URL del webhook (n8n)</label>
                <input type="text" id="newWebhookUrl" wire:model="newWebhookUrl"
                    placeholder="http://n8n:5678/webhook/whatsapp">
                <p class="wa-form-hint">URL donde n8n recibirá los mensajes de WhatsApp</p>
                @error('newWebhookUrl') <p class="wa-form-error">{{ $message }}</p> @enderror
            </div>

            <div class="wa-actions" style="justify-content: flex-start;">
                <x-filament::button size="sm" color="success" wire:click="createInstance">
                    ✅ Crear Instancia
                </x-filament::button>
                <x-filament::button size="sm" color="gray" wire:click="toggleCreateForm">
                    Cancelar
                </x-filament::button>
            </div>

            @else
            {{-- Modo: Configurar webhook de instancia existente --}}
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 0.25rem;">
                ⚙️ Configurar Webhook
            </h3>
            <p style="font-size: 0.8rem; opacity: 0.6; margin: 0 0 1rem;">
                Configura la URL y los eventos del webhook para la instancia <strong>{{
                    config('services.evolution.instance', 'HunabkuBot') }}</strong>.
                Se activarán: MESSAGES_UPSERT, CONNECTION_UPDATE, SEND_MESSAGE.
            </p>

            <div class="wa-form-group">
                <label for="newWebhookUrl">URL del webhook (n8n)</label>
                <input type="text" id="newWebhookUrl" wire:model="newWebhookUrl"
                    placeholder="http://n8n:5678/webhook/whatsapp">
                <p class="wa-form-hint">URL donde n8n recibirá los mensajes de WhatsApp</p>
                @error('newWebhookUrl') <p class="wa-form-error">{{ $message }}</p> @enderror
            </div>

            <div class="wa-actions" style="justify-content: flex-start;">
                <x-filament::button size="sm" color="success" wire:click="configureWebhook">
                    ✅ Guardar Webhook
                </x-filament::button>
                <x-filament::button size="sm" color="gray" wire:click="toggleCreateForm">
                    Cancelar
                </x-filament::button>
            </div>
            @endif
        </div>
        @endif

        {{-- QR CODE (only if disconnected and instance exists) --}}
        @if($connectionState !== 'open' && $connectionState !== 'loading' && $connectionState !== 'not_found' &&
        $instanceExists)
        <div class="wa-qr-section" wire:poll.10s="fetchQR">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 0.5rem;">
                Escanea el código QR
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
                <x-filament::button size="sm" color="info" wire:click="regenerateQR">
                    Regenerar QR
                </x-filament::button>
            </div>
        </div>
        @endif

        {{-- STEPS --}}
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem;">
            ¿Cómo conectar?
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
            Información técnica
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
                <a href="http://localhost:8080/manager/" target="_blank"
                    style="font-size: 0.8rem; color: #3b82f6; text-decoration: none; font-weight: 600;">
                    Acceso a Evolution API
                </a>
            </div>
        </div>
    
                </div>
            </div>
        </div>

        {{-- Boton guardar WhatsApp --}}
        <div style="text-align: right;">
            <x-filament::button wire:click="saveWhatsAppSettings" color="success" size="lg">
                Guardar Proveedor WhatsApp
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>