<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Header Card --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-950 dark:text-white flex items-center gap-2">
                        ✨ Resumen Inteligente de Correos
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Conecta tu bandeja de entrada de Gmail y obtén un resumen ejecutivo generado por IA.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($lastGenerated)
                        <span class="text-xs text-gray-400">
                            Último: {{ $lastGenerated }}
                        </span>
                    @endif
                    <x-filament::button
                        wire:click="generateDigest"
                        wire:loading.attr="disabled"
                        icon="heroicon-o-arrow-path"
                        color="primary"
                        size="lg"
                    >
                        <span wire:loading.remove wire:target="generateDigest">
                            {{ $summary ? '🔄 Regenerar Resumen' : '🚀 Generar Resumen de Hoy' }}
                        </span>
                        <span wire:loading wire:target="generateDigest">
                            ⏳ Analizando correos...
                        </span>
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Loading State --}}
        <div wire:loading wire:target="generateDigest">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-8">
                <div class="flex flex-col items-center justify-center gap-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"></div>
                    <div class="text-center">
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">Procesando correos...</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Conectando a Gmail, leyendo correos y generando resumen con IA</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Card --}}
        @if($summary)
            <div wire:loading.remove wire:target="generateDigest" class="space-y-4">
                {{-- Stats --}}
                @if($emailsCount !== null)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-3">
                                    <span class="text-2xl">📧</span>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $emailsCount }}</p>
                                    <p class="text-sm text-gray-500">Correos analizados</p>
                                </div>
                            </div>
                        </div>
                        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 p-3">
                                    <span class="text-2xl">🤖</span>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">Groq AI</p>
                                    <p class="text-sm text-gray-500">Motor de IA</p>
                                </div>
                            </div>
                        </div>
                        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-purple-50 dark:bg-purple-900/20 p-3">
                                    <span class="text-2xl">🕐</span>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lastGenerated }}</p>
                                    <p class="text-sm text-gray-500">Generado a las</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- AI Summary --}}
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        📝 Resumen del Día
                    </h3>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! \Illuminate\Support\Str::markdown($summary) !!}
                    </div>
                </div>

                {{-- Email List --}}
                @if($emailsList && count($emailsList) > 0)
                    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            📥 Correos Procesados ({{ count($emailsList) }})
                        </h3>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($emailsList as $email)
                                <div class="py-3 flex items-start gap-3">
                                    <div class="rounded-full bg-gray-100 dark:bg-gray-800 p-2 mt-0.5">
                                        <span class="text-sm">✉️</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white text-sm truncate">
                                            {{ $email['subject'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $email['from_name'] }} &bull; {{ $email['date'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- History --}}
        @if(count($this->history) > 0)
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    📅 Historial de Resúmenes
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($this->history as $digest)
                        <button
                            wire:click="loadHistoricDigest({{ $digest->id }})"
                            class="text-left p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors"
                        >
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">
                                📅 {{ $digest->digest_date->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $digest->emails_count }} correos analizados
                            </p>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
