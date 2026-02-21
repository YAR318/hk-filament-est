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
        </style>

        {{-- Correo del encargado --}}
        <div class="settings-card">
            <h3>📧 Correo del Encargado</h3>
            <p class="desc">Este correo recibirá las notificaciones de citas (confirmación, cancelación, recordatorios).
            </p>

            <div class="form-group">
                <label for="admin_email">Correo electrónico</label>
                <input type="email" id="admin_email" wire:model="admin_email" placeholder="tucorreo@gmail.com">
                <p class="form-hint">Puedes cambiarlo cuando quieras</p>
                @error('admin_email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Horario laboral --}}
        <div class="settings-card">
            <h3>🕘 Horario Laboral</h3>
            <p class="desc">Las citas solo se podrán agendar dentro de este horario.</p>

            <div class="hours-row">
                <div class="form-group">
                    <label for="work_start_hour">Hora de inicio</label>
                    <select id="work_start_hour" wire:model="work_start_hour">
                        @for($h = 6; $h <= 20; $h++) <option value="{{ $h }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00
                            </option>
                            @endfor
                    </select>
                </div>
                <span class="hours-separator">→</span>
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

        {{-- Días laborales --}}
        <div class="settings-card">
            <h3>📅 Días Laborales</h3>
            <p class="desc">Selecciona los días en que se pueden agendar citas.</p>

            <div class="form-group">
                <input type="hidden" wire:model="work_days">
                @php
                $dayNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
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

        {{-- Botón guardar --}}
        <div style="text-align: right;">
            <x-filament::button wire:click="saveSettings" color="success" size="lg">
                💾 Guardar Configuración
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>