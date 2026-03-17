<x-filament-panels::page>
    <div style="max-width: 640px;">
        <style>
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
            <div x-data="{ show: @entangle('whatsapp_provider') }" x-show="show === 'evolution'" x-transition>
                <p style="font-size: 0.85rem; opacity: 0.7; padding-top: 8px;">
                    Evolution API se configura desde
                    <a href="{{ route('filament.admin.pages.connect-whatsapp') }}"
                        style="color: #6366f1; text-decoration: underline;">Conectar WhatsApp</a>.
                </p>
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