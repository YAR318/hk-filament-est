<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-950 dark:text-white">
                        📬 Resumen diario de correos
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        La IA lee todos tus correos del día y te da un resumen con los puntos importantes.
                    </p>
                </div>
                <x-filament::button
                    wire:click="generateDigest"
                    wire:loading.attr="disabled"
                    icon="heroicon-o-arrow-path"
                    color="primary"
                >
                    <span wire:loading.remove wire:target="generateDigest">
                        {{ $summary ? '🔄 Actualizar' : '🚀 Generar Resumen' }}
                    </span>
                    <span wire:loading wire:target="generateDigest">
                        ⏳ Analizando...
                    </span>
                </x-filament::button>
            </div>
        </div>

        {{-- Loading --}}
        <div wire:loading wire:target="generateDigest">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-8 text-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary-500 mx-auto"></div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Conectando a Gmail y generando resumen con IA...</p>
            </div>
        </div>

        {{-- Resumen principal --}}
        @if($summary)
            <div wire:loading.remove wire:target="generateDigest" class="space-y-4">

                {{-- Info rápida --}}
                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 px-1">
                    <span>📧 {{ $emailsCount ?? 0 }} correos analizados</span>
                    <span>•</span>
                    <span>🤖 Groq AI</span>
                    @if($lastGenerated)
                        <span>•</span>
                        <span>🕐 {{ $lastGenerated }}</span>
                    @endif
                </div>

                {{-- El resumen de la IA (lo principal) --}}
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
                    <div class="prose dark:prose-invert max-w-none prose-sm">
                        {!! \Illuminate\Support\Str::markdown($summary) !!}
                    </div>
                </div>

                {{-- Botón para ver detalles --}}
                @if($emailsList && count($emailsList) > 0)
                    <div x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400 hover:underline px-1"
                        >
                            <span x-text="open ? '▼ Ocultar correos procesados' : '▶ Ver correos procesados ({{ count($emailsList) }})'"></span>
                        </button>

                        <div x-show="open" x-collapse class="mt-3">
                            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($emailsList as $email)
                                        <div class="py-2.5 flex items-center gap-3">
                                            <span class="text-sm shrink-0">✉️</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-900 dark:text-white text-sm truncate">
                                                    {{ $email['subject'] }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $email['from_name'] }} • {{ $email['date'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Historial --}}
        @if(count($this->history) > 0)
            <div x-data="{ open: false }">
                <button
                    @click="open = !open"
                    class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 px-1"
                >
                    <span x-text="open ? '▼ Ocultar historial' : '▶ Ver historial de resúmenes'"></span>
                </button>

                <div x-show="open" x-collapse class="mt-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @foreach($this->history as $digest)
                            <button
                                wire:click="loadHistoricDigest({{ $digest->id }})"
                                class="text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors bg-white dark:bg-gray-900"
                            >
                                <p class="font-medium text-gray-900 dark:text-white text-sm">
                                    📅 {{ $digest->digest_date->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $digest->emails_count }} correos
                                </p>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
