<x-filament-panels::page>
    {{-- Estilos Inline para garantizar visualización sin recompilar assets --}}
    <style>
        .chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 200px);
            /* Ajuste para header/footer */
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            overflow: hidden;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .dark .chat-container {
            background-color: #111827;
            border-color: #374151;
            color: white;
        }

        /* Header */
        .chat-header {
            background-color: white;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dark .chat-header {
            background-color: #1f2937;
            border-color: #374151;
        }

        /* Messages Area */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background-color: #f3f4f6;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .dark .chat-messages {
            background-color: #030712;
            background-image: radial-gradient(#374151 1px, transparent 1px);
        }

        /* Message Bubbles */
        .message-row {
            display: flex;
            width: 100%;
        }

        .message-row.user {
            justify-content: flex-start;
        }

        .message-row.assistant {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            position: relative;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            line-height: 1.5;
        }

        /* User Bubble (Left) */
        .message-bubble.user {
            background-color: white;
            color: #111827;
            border-bottom-left-radius: 0;
            border: 1px solid #e5e7eb;
        }

        .dark .message-bubble.user {
            background-color: #1f2937;
            color: white;
            border-color: #374151;
        }

        /* Assistant Bubble (Right) */
        .message-bubble.assistant {
            background-color: #3b82f6;
            /* Blue-500 */
            color: white;
            border-bottom-right-radius: 0;
        }

        .dark .message-bubble.assistant {
            background-color: #2563eb;
            /* Blue-600 */
        }

        /* Timestamp */
        .message-time {
            font-size: 0.7rem;
            margin-top: 0.25rem;
            opacity: 0.7;
            text-align: right;
            display: block;
        }

        /* Avatar Circle */
        .avatar-circle {
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.25rem;
            color: white;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            margin-right: 1rem;
            flex-shrink: 0;
        }

        /* Status Dot */
        .status-dot {
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 9999px;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-active {
            background-color: #22c55e;
        }

        .status-archived {
            background-color: #eab308;
        }

        .status-blocked {
            background-color: #ef4444;
        }
    </style>

    <div class="chat-container">
        {{-- Header --}}
        <div class="chat-header">
            <div style="display: flex; align-items: center;">
                <div class="relative">
                    @if($this->record->profile_pic_url)
                    <img src="{{ $this->record->profile_pic_url }}" class="avatar-circle" style="object-fit: cover;">
                    @else
                    <div class="avatar-circle">
                        {{ substr($this->record->contact_name ?? $this->record->phone_number, 0, 1) }}
                    </div>
                    @endif
                </div>
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0;">
                        {{ $this->record->contact_name ?? 'Sin nombre' }}</h2>
                    <p style="font-size: 0.875rem; opacity: 0.7; margin: 0;">{{ $this->record->phone_number }}</p>
                </div>
            </div>
            <div style="display: flex; align-items: center;">
                <span
                    class="status-dot @if($this->record->status === 'active') status-active @elseif($this->record->status === 'archived') status-archived @else status-blocked @endif"></span>
                <span style="font-size: 0.875rem; font-weight: 500;">
                    {{ ucfirst($this->record->status) }}
                </span>
            </div>
        </div>

        {{-- Messages --}}
        <div class="chat-messages" id="chat-messages-container" wire:poll.3s>
            @forelse($this->getMessages() as $message)
            <div class="message-row {{ $message->role === 'user' ? 'user' : 'assistant' }}">
                <div class="message-bubble {{ $message->role === 'user' ? 'user' : 'assistant' }}">
                    <div style="white-space: pre-wrap;">{{ $message->content }}</div>
                    <span class="message-time">
                        {{ $message->sent_at->format('h:i A') }}
                        @if($message->role === 'assistant') ✓ @endif
                    </span>
                </div>
            </div>
            @empty
            <div style="text-align: center; margin-top: 4rem; opacity: 0.5;">
                <svg style="width: 4rem; height: 4rem; margin: 0 auto; color: #9ca3af;" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                    </path>
                </svg>
                <h3 style="margin-top: 1rem; font-weight: 600;">No hay mensajes</h3>
                <p>La conversación comenzará cuando envíes o recibas el primer mensaje.</p>
            </div>
            @endforelse

            {{-- Script inline para mantener scroll abajo tras actualización Livewire --}}
            <script>
                document.addEventListener("livewire:navigated", () => {
                    var objDiv = document.getElementById("chat-messages-container");
                    if (objDiv) objDiv.scrollTop = objDiv.scrollHeight;
                });
                // Hook para updates de Livewire (polling)
                document.addEventListener("livewire:processed", () => {
                    var objDiv = document.getElementById("chat-messages-container");
                    // Solo scroll si ya estaba abajo o es la primera carga (lógica simple por ahora: siempre scroll por polling)
                    if (objDiv) objDiv.scrollTop = objDiv.scrollHeight;
                });
            </script>
        </div>

        {{-- Footer (Simple) --}}
        <div style="padding: 1rem; background-color: inherit; border-top: 1px solid inherit;" class="chat-header">
            <div style="display: flex; gap: 0.5rem; width: 100%;">
                <input type="text" wire:model="newMessage" wire:keydown.enter="sendMessage"
                    placeholder="Escribe un mensaje..."
                    style="flex: 1; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background-color: #f9fafb;"
                    class="dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <button wire:click="sendMessage"
                    style="padding: 0.5rem 1.5rem; background-color: #3b82f6; color: white; border-radius: 0.5rem; border: none; font-weight: 600; cursor: pointer;">
                    Enviar
                </button>
            </div>
            <p style="text-align: center; font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">
                El envío manual está en desarrollo.
            </p>
        </div>
    </div>

    <script>
        // Auto scroll to bottom
        window.addEventListener('load', function () {
            var objDiv = document.getElementById("chat-messages-container");
            if (objDiv) {
                objDiv.scrollTop = objDiv.scrollHeight;
            }
        });
    </script>
</x-filament-panels::page>