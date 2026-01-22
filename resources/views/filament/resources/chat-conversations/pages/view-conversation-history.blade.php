<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Información del contacto --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $this->record->contact_name ?? 'Sin nombre' }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->record->phone_number }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($this->record->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($this->record->status === 'archived') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @endif">
                        @if($this->record->status === 'active') Activa
                        @elseif($this->record->status === 'archived') Archivada
                        @else Bloqueada
                        @endif
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $this->getMessages()->count() }} mensajes
                    </p>
                </div>
            </div>
        </div>

        {{-- Historial de mensajes --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">
                    Historial de Mensajes
                </h4>
                
                <div class="space-y-4 max-h-[600px] overflow-y-auto">
                    @forelse($this->getMessages() as $message)
                        <div class="flex {{ $message->role === 'user' ? 'justify-start' : 'justify-end' }}">
                            <div class="max-w-[70%]">
                                {{-- Etiqueta de rol --}}
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 {{ $message->role === 'user' ? 'text-left' : 'text-right' }}">
                                    @if($message->role === 'user')
                                        <span class="font-medium">Cliente</span>
                                    @elseif($message->role === 'assistant')
                                        <span class="font-medium">Asistente</span>
                                    @else
                                        <span class="font-medium">Sistema</span>
                                    @endif
                                    · {{ $message->sent_at->format('d/m/Y H:i') }}
                                </div>
                                
                                {{-- Burbuja de mensaje --}}
                                <div class="rounded-lg px-4 py-3 
                                    @if($message->role === 'user')
                                        bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white
                                    @elseif($message->role === 'assistant')
                                        bg-blue-500 text-white
                                    @else
                                        bg-yellow-100 dark:bg-yellow-900 text-yellow-900 dark:text-yellow-100
                                    @endif">
                                    <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay mensajes</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Esta conversación aún no tiene mensajes.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Botón para volver --}}
        <div class="flex justify-end">
            <a href="{{ ChatConversationResource::getUrl('index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                Volver a conversaciones
            </a>
        </div>
    </div>
</x-filament-panels::page>
