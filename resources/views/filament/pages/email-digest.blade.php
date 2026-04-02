<x-filament-panels::page>
    <style>
        .ed-page {
            max-width: 900px;
            margin: 0 auto;
        }

        .ed-card {
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            background-color: white;
        }

        .ed-header-card {
            text-align: center;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 100%);
            position: relative;
            overflow: hidden;
        }

        .dark .ed-card {
            background-color: #1f2937;
            border-color: #374151;
        }

        .dark .ed-header-card {
            background: linear-gradient(135deg, #064e3b22 0%, #0e374422 100%);
        }

        .ed-title {
            font-size: 1.3rem;
            font-weight: 800;
            margin: 0 0 0.5rem;
        }

        .ed-subtitle {
            font-size: 0.85rem;
            opacity: 0.6;
            margin: 0 0 1.5rem;
        }

        .ed-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .ed-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .ed-info-card {
            border-radius: 0.75rem;
            padding: 1.25rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            text-align: center;
        }

        .dark .ed-info-card {
            background-color: #1f2937;
            border-color: #374151;
        }

        .ed-info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 0.4rem;
        }

        .ed-info-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
        }

        .dark .ed-info-value {
            color: #f9fafb;
        }

        .ed-spinner {
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid #e5e7eb;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: ed-spin 0.8s linear infinite;
            display: inline-block;
            margin: 0 auto 1rem;
        }

        @keyframes ed-spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Lists */
        .ed-list-item {
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .ed-list-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .dark .ed-list-item {
            border-color: #374151;
        }

        .ed-list-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #111827;
        }

        .dark .ed-list-title {
            color: #f9fafb;
        }

        .ed-list-desc {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .dark .ed-list-desc {
            color: #9ca3af;
        }

        .ed-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #3b82f6;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .ed-toggle-btn:hover {
            text-decoration: underline;
        }

        .ed-historic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .ed-historic-btn {
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            text-align: left;
            cursor: pointer;
            transition: all 0.15s;
        }

        .ed-historic-btn:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }

        .dark .ed-historic-btn {
            background-color: #1f2937;
            border-color: #374151;
        }

        .dark .ed-historic-btn:hover {
            border-color: #3b82f6;
            background-color: #111827;
        }
    </style>

    <div class="ed-page">

        {{-- Header Card --}}
        <div class="ed-card ed-header-card">
            <h2 class="ed-title">
                Resumen Inteligente de Correos
            </h2>
            <p class="ed-subtitle">
                Conecta tu bandeja de entrada de Gmail y obtén un resumen ejecutivo generado por IA.
            </p>
            <div class="ed-actions">
                <x-filament::button
                    wire:click="generateDigest"
                    wire:loading.attr="disabled"
                    color="primary"
                >
                    <span wire:loading.remove wire:target="generateDigest">
                        {{ $summary ? 'Actualizar Resumen' : 'Generar Resumen de Hoy' }}
                    </span>
                    <span wire:loading wire:target="generateDigest">
                        Analizando...
                    </span>
                </x-filament::button>
            </div>
        </div>

        {{-- Loading Section --}}
        <div wire:loading wire:target="generateDigest" style="width: 100%;">
            <div class="ed-card" style="text-align: center;">
                <div class="ed-spinner"></div>
                <h2 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Procesando correos</h2>
                <p style="font-size: 0.85rem; opacity: 0.6; margin: 0.5rem 0 0;">
                    Conectando a Gmail, leyendo correos y generando resumen con IA.
                </p>
            </div>
        </div>

        @if($summary)
            <div wire:loading.remove wire:target="generateDigest">
                
                {{-- Stats Grid --}}
                @if($emailsCount !== null)
                    <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem;">Metricas del Dia</h3>
                    <div class="ed-info-grid">
                        <div class="ed-info-card">
                            <p class="ed-info-label">Correos Analizados</p>
                            <p class="ed-info-value">{{ $emailsCount }}</p>
                        </div>
                        <div class="ed-info-card">
                            <p class="ed-info-label">Motor de IA</p>
                            <p class="ed-info-value">Groq AI</p>
                        </div>
                        <div class="ed-info-card">
                            <p class="ed-info-label">Ultima Generacion</p>
                            <p class="ed-info-value">{{ $lastGenerated }}</p>
                        </div>
                    </div>
                @endif

                {{-- AI Summary --}}
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem;">Resumen Ejecutivo</h3>
                <div class="ed-card">
                    <div class="prose dark:prose-invert max-w-none prose-sm">
                        {!! \Illuminate\Support\Str::markdown($summary) !!}
                    </div>
                </div>

                {{-- Email List (Collapsible) --}}
                @if($emailsList && count($emailsList) > 0)
                    <div x-data="{ open: false }" style="margin-bottom: 1.5rem;">
                        <button @click="open = !open" class="ed-toggle-btn">
                            <span x-text="open ? 'Ocultar correos procesados' : 'Ver correos procesados ({{ count($emailsList) }})'"></span>
                        </button>

                        <div x-show="open" x-collapse style="margin-top: 1rem;">
                            <div class="ed-card" style="margin-bottom: 0;">
                                @foreach($emailsList as $email)
                                    <div class="ed-list-item">
                                        <p class="ed-list-title">{{ $email['subject'] }}</p>
                                        <p class="ed-list-desc">{{ $email['from_name'] }} | {{ $email['date'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- History (Collapsible) --}}
        @if(count($this->history) > 0)
            <div x-data="{ open: false }">
                <button @click="open = !open" class="ed-toggle-btn">
                    <span x-text="open ? 'Ocultar historial de resumenes' : 'Ver historial de resumenes'"></span>
                </button>

                <div x-show="open" x-collapse style="margin-top: 1rem;">
                    <div class="ed-historic-grid">
                        @foreach($this->history as $digest)
                            <button
                                wire:click="loadHistoricDigest({{ $digest->id }})"
                                class="ed-historic-btn"
                            >
                                <p class="ed-list-title">{{ $digest->digest_date->format('d/m/Y') }}</p>
                                <p class="ed-list-desc" style="margin-top: 0.25rem;">{{ $digest->emails_count }} correos analizados</p>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
