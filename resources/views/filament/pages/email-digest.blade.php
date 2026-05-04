<x-filament-panels::page>
    <style>
        .ed-page {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Header */
        .ed-header {
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
            position: relative;
            overflow: hidden;
        }

        .dark .ed-header {
            background: linear-gradient(135deg, #1e3a5f22 0%, #064e3b22 100%);
            border-color: #374151;
        }

        .ed-header h2 {
            font-size: 1.3rem;
            font-weight: 800;
            margin: 0 0 0.25rem;
        }

        .ed-header p {
            font-size: 0.85rem;
            opacity: 0.6;
            margin: 0 0 1.25rem;
        }

        /* Cards */
        .ed-card {
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            background-color: white;
        }

        .dark .ed-card {
            background-color: #1f2937;
            border-color: #374151;
        }

        /* Stats row */
        .ed-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 640px) {
            .ed-stats { grid-template-columns: 1fr; }
        }

        .ed-stat {
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            text-align: center;
        }

        .dark .ed-stat {
            background-color: #1f2937;
            border-color: #374151;
        }

        .ed-stat-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #6b7280;
            margin: 0 0 0.3rem;
        }

        .ed-stat-value {
            font-size: 1.15rem;
            font-weight: 800;
            margin: 0;
        }

        /* Summary section */
        .ed-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            margin: 0 0 0.75rem;
        }

        .ed-summary-card {
            border-radius: 0.75rem;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            border-left: 4px solid #3b82f6;
        }

        .dark .ed-summary-card {
            background-color: #1f2937;
            border-color: #374151;
            border-left-color: #3b82f6;
        }

        .ed-summary-card .prose {
            font-size: 0.9rem;
            line-height: 1.7;
        }

        /* Viewing label */
        .ed-viewing-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            background-color: #dbeafe;
            color: #1e40af;
            margin-bottom: 1rem;
        }

        .dark .ed-viewing-label {
            background-color: #1e3a5f;
            color: #93c5fd;
        }

        /* History */
        .ed-history-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 0.75rem;
        }

        .ed-history-btn {
            border-radius: 0.75rem;
            padding: 0.9rem 1rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            text-align: left;
            cursor: pointer;
            transition: all 0.15s;
            width: 100%;
        }

        .ed-history-btn:hover {
            border-color: #3b82f6;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .dark .ed-history-btn {
            background-color: #1f2937;
            border-color: #374151;
        }

        .dark .ed-history-btn:hover {
            border-color: #3b82f6;
        }

        .ed-history-btn.active {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .dark .ed-history-btn.active {
            background-color: #1e3a5f44;
            border-color: #3b82f6;
        }

        .ed-history-date {
            font-size: 0.85rem;
            font-weight: 700;
            margin: 0;
        }

        .ed-history-meta {
            font-size: 0.7rem;
            color: #6b7280;
            margin: 0.2rem 0 0;
        }

        /* Detail toggle */
        .ed-detail-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem 0;
            transition: color 0.15s;
        }

        .ed-detail-toggle:hover {
            color: #3b82f6;
        }

        .ed-detail-toggle svg {
            width: 1rem;
            height: 1rem;
            transition: transform 0.15s;
        }

        .ed-email-row {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .ed-email-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .dark .ed-email-row {
            border-color: #374151;
        }

        .ed-email-subject {
            font-size: 0.85rem;
            font-weight: 600;
            margin: 0;
        }

        .ed-email-meta {
            font-size: 0.72rem;
            color: #6b7280;
            margin: 0.15rem 0 0;
        }

        /* Spinner */
        .ed-spinner {
            width: 2rem;
            height: 2rem;
            border: 3px solid #e5e7eb;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: ed-spin 0.8s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes ed-spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <div class="ed-page">

        {{-- HEADER --}}
        <div class="ed-header">
            <h2>Resumen Inteligente de Correos</h2>
            <p>Lee todos tus correos del dia y genera un resumen ejecutivo con inteligencia artificial.</p>
            <x-filament::button
                wire:click="generateDigest"
                wire:loading.attr="disabled"
                color="primary"
            >
                <span wire:loading.remove wire:target="generateDigest">
                    {{ $summary ? 'Actualizar Resumen' : 'Generar Resumen de Hoy' }}
                </span>
                <span wire:loading wire:target="generateDigest">
                    Analizando correos...
                </span>
            </x-filament::button>
        </div>

        {{-- LOADING --}}
        <div wire:loading wire:target="generateDigest">
            <div class="ed-card" style="text-align: center; padding: 3rem;">
                <div class="ed-spinner"></div>
                <p style="font-weight: 700; margin: 0;">Procesando correos</p>
                <p style="font-size: 0.8rem; opacity: 0.5; margin: 0.25rem 0 0;">Conectando a Gmail y generando resumen con IA...</p>
            </div>
        </div>

        {{-- CONTENT --}}
        @if($summary)
            <div wire:loading.remove wire:target="generateDigest">

                {{-- Date label --}}
                @if($viewingDate)
                    <div class="ed-viewing-label">
                        Resumen del {{ $viewingDate }}
                    </div>
                @endif

                {{-- Stats --}}
                <div class="ed-stats">
                    <div class="ed-stat">
                        <p class="ed-stat-label">Correos analizados</p>
                        <p class="ed-stat-value">{{ $emailsCount ?? 0 }}</p>
                    </div>
                    <div class="ed-stat">
                        <p class="ed-stat-label">Motor de IA</p>
                        <p class="ed-stat-value">Groq AI</p>
                    </div>
                    <div class="ed-stat">
                        <p class="ed-stat-label">Generado a las</p>
                        <p class="ed-stat-value">{{ $lastGenerated ?? '--' }}</p>
                    </div>
                </div>

                {{-- SUMMARY (main content) --}}
                <h3 class="ed-section-title">Resumen Ejecutivo</h3>
                <div class="ed-summary-card">
                    <div class="prose dark:prose-invert max-w-none prose-sm">
                        {!! \Illuminate\Support\Str::markdown($summary) !!}
                    </div>
                </div>

                {{-- DETAIL: email list --}}
                @if($emailsList && count($emailsList) > 0)
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="ed-detail-toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :style="open ? 'transform: rotate(90deg)' : ''">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                            Ver detalle de correos procesados ({{ count($emailsList) }})
                        </button>

                        <div x-show="open" x-collapse>
                            <div class="ed-card" style="margin-top: 0.5rem;">
                                @foreach($emailsList as $email)
                                    <div class="ed-email-row">
                                        <p class="ed-email-subject">{{ $email['subject'] }}</p>
                                        <p class="ed-email-meta">{{ $email['from_name'] }} &middot; {{ $email['date'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- HISTORY --}}
        @if(count($this->history) > 0)
            <div style="margin-top: 2rem;">
                <h3 class="ed-section-title">Resumenes anteriores</h3>
                <div class="ed-history-grid">
                    @foreach($this->history as $digest)
                        <button
                            wire:click="loadHistoricDigest({{ $digest->id }})"
                            class="ed-history-btn {{ $viewingDate === $digest->digest_date->format('d/m/Y') ? 'active' : '' }}"
                        >
                            <p class="ed-history-date">{{ $digest->digest_date->format('d/m/Y') }}</p>
                            <p class="ed-history-meta">{{ $digest->emails_count }} correos</p>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
