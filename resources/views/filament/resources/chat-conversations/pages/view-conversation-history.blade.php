<x-filament-panels::page>
    <style>
        .detail-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 1rem;
            height: calc(100vh - 180px);
        }

        @media (max-width: 1024px) {
            .detail-layout {
                grid-template-columns: 1fr;
                height: auto;
            }
        }

        /* Sidebar */
        .detail-sidebar {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .dark .detail-sidebar {
            background-color: #1f2937;
            border-color: #374151;
        }

        .sidebar-section {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .sidebar-section {
            border-color: #374151;
        }

        .sidebar-section:last-child {
            border-bottom: none;
        }

        .sidebar-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .sidebar-value {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-green {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-red {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-gray {
            background-color: #f3f4f6;
            color: #374151;
        }

        .badge-yellow {
            background-color: #fef3c7;
            color: #92400e;
        }

        .dark .badge-green {
            background-color: #166534;
            color: #dcfce7;
        }

        .dark .badge-red {
            background-color: #991b1b;
            color: #fee2e2;
        }

        .dark .badge-blue {
            background-color: #1e40af;
            color: #dbeafe;
        }

        .dark .badge-gray {
            background-color: #374151;
            color: #d1d5db;
        }

        .dark .badge-yellow {
            background-color: #92400e;
            color: #fef3c7;
        }

        /* Action Buttons */
        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.65rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            margin-bottom: 0.5rem;
        }

        .action-btn:hover {
            filter: brightness(0.9);
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-success {
            background-color: #22c55e;
            color: white;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-warning {
            background-color: #f59e0b;
            color: white;
        }

        .btn-gray {
            background-color: #6b7280;
            color: white;
        }

        /* Chat Container */
        .chat-container {
            display: flex;
            flex-direction: column;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .dark .chat-container {
            background-color: #111827;
            border-color: #374151;
        }

        .chat-header {
            background-color: white;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dark .chat-header {
            background-color: #1f2937;
            border-color: #374151;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            background-color: #f3f4f6;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .dark .chat-messages {
            background-color: #030712;
            background-image: radial-gradient(#374151 1px, transparent 1px);
        }

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
            max-width: 75%;
            padding: 0.6rem 0.85rem;
            border-radius: 0.85rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            line-height: 1.5;
            font-size: 0.9rem;
        }

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

        .message-bubble.assistant {
            background-color: #3b82f6;
            color: white;
            border-bottom-right-radius: 0;
        }

        .dark .message-bubble.assistant {
            background-color: #2563eb;
        }

        .message-time {
            font-size: 0.65rem;
            margin-top: 0.2rem;
            opacity: 0.7;
            text-align: right;
            display: block;
        }

        .chat-footer {
            padding: 0.75rem 1rem;
            background-color: white;
            border-top: 1px solid #e5e7eb;
        }

        .dark .chat-footer {
            background-color: #1f2937;
            border-color: #374151;
        }

        .chat-input-group {
            display: flex;
            gap: 0.5rem;
        }

        .chat-input {
            flex: 1;
            padding: 0.65rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background-color: #f9fafb;
            font-size: 0.9rem;
            outline: none;
        }

        .chat-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .dark .chat-input {
            background-color: #374151;
            border-color: #4b5563;
            color: white;
        }

        .send-btn {
            padding: 0.5rem 1.25rem;
            background-color: #3b82f6;
            color: white;
            border-radius: 0.5rem;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .send-btn:hover {
            background-color: #2563eb;
        }

        .chat-closed-banner {
            text-align: center;
            padding: 1rem;
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .dark .chat-closed-banner {
            background-color: #451a03;
            color: #fde68a;
        }

        .avatar-circle {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.5rem;
            color: white;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            flex-shrink: 0;
        }

        .select-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            background-color: #f9fafb;
            margin-bottom: 0.5rem;
        }

        .dark .select-input {
            background-color: #374151;
            border-color: #4b5563;
            color: white;
        }
    </style>

    <div class="detail-layout">
        {{-- SIDEBAR --}}
        <div class="detail-sidebar">
            {{-- Contact Info --}}
            <div class="sidebar-section" style="text-align: center;">
                <div style="display: flex; justify-content: center; margin-bottom: 0.75rem;">
                    @if($this->record->profile_pic_url)
                    <img src="{{ $this->record->profile_pic_url }}" class="avatar-circle" style="object-fit: cover;">
                    @else
                    <div class="avatar-circle">
                        {{ substr($this->record->contact_name ?? $this->record->phone_number, 0, 1) }}
                    </div>
                    @endif
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">
                    {{ $this->record->contact_name ?? 'Sin nombre' }}
                </h3>
                <p style="font-size: 0.85rem; opacity: 0.6; margin: 0.25rem 0 0;">
                    {{ $this->record->phone_number }}
                </p>
            </div>

            {{-- Status Info --}}
            <div class="sidebar-section">
                <div class="info-row">
                    <span class="sidebar-label">Estado</span>
                    <span class="badge {{ match($this->record->status) {
                        'active' => 'badge-green',
                        'en_proceso' => 'badge-blue',
                        'resuelto' => 'badge-gray',
                        'blocked' => 'badge-red',
                        default => 'badge-yellow',
                    } }}">
                        {{ match($this->record->status) {
                        'active' => 'Activa',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'blocked' => 'Bloqueada',
                        'archived' => 'Archivada',
                        default => $this->record->status,
                        } }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="sidebar-label">Bot</span>
                    <span class="badge {{ $this->record->is_bot_active ? 'badge-green' : 'badge-red' }}">
                        {{ $this->record->is_bot_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="sidebar-label">Operador</span>
                    <span class="badge {{ $this->record->assigned_to ? 'badge-blue' : 'badge-gray' }}">
                        {{ $this->record->assignedOperator?->name ?? 'Sin asignar' }}
                    </span>
                </div>
                @if($this->record->last_message_at)
                <div class="info-row">
                    <span class="sidebar-label">Último msg</span>
                    <span class="sidebar-value" style="font-size: 0.8rem;">
                        {{ $this->record->last_message_at->diffForHumans() }}
                    </span>
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="sidebar-section">
                <p class="sidebar-label" style="margin-bottom: 0.75rem;">Acciones</p>

                {{-- OPERATOR: Take Chat --}}
                @if($this->canTakeChat())
                <button wire:click="takeChat" class="action-btn btn-primary"
                    wire:confirm="¿Tomar esta conversación? Serás asignado como operador y el bot se desactivará.">
                    Tomar Chat
                </button>
                @endif

                {{-- OPERATOR & SUPERVISOR: Close Chat --}}
                @if($this->canCloseChat())
                <button wire:click="closeChat" class="action-btn btn-warning"
                    wire:confirm="¿Cerrar esta conversación? Se marcará como resuelta y el bot se reactivará.">
                    Cerrar Chat
                </button>
                @endif

                {{-- SUPERVISOR/ADMIN ONLY --}}
                @if($this->isManager())
                {{-- Assign Operator --}}
                <div style="margin-bottom: 0.5rem;">
                    <select wire:model="selectedOperator" class="select-input">
                        <option value="">Seleccionar operador...</option>
                        @foreach($this->getOperatorOptions() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <button wire:click="assignOperator" class="action-btn btn-primary">
                        Asignar Operador
                    </button>
                </div>

                {{-- Toggle Bot --}}
                @if($this->record->is_bot_active)
                <button wire:click="toggleBot" class="action-btn btn-danger">
                    Desactivar Bot
                </button>
                @else
                <button wire:click="toggleBot" class="action-btn btn-success">
                    Activar Bot
                </button>
                @endif
                @endif

                {{-- Back to list --}}
                <a href="/admin/chat-conversations" class="action-btn btn-gray"
                    style="text-decoration: none; text-align: center; margin-top: 0.5rem;">
                    ← Volver a la lista
                </a>
            </div>
        </div>

        {{-- CHAT AREA --}}
        <div class="chat-container">
            {{-- Chat Header --}}
            <div class="chat-header">
                <div>
                    <strong style="font-size: 0.95rem;">{{ $this->record->contact_name ?? $this->record->phone_number
                        }}</strong>
                    <span style="font-size: 0.75rem; opacity: 0.6; margin-left: 0.5rem;">
                        {{ $this->record->messages()->count() }} mensajes
                    </span>
                </div>
                <div>
                    @if($this->record->status === 'en_proceso')
                    <span class="badge badge-blue">En atención</span>
                    @elseif($this->record->status === 'resuelto')
                    <span class="badge badge-gray">Resuelto</span>
                    @elseif($this->record->is_bot_active)
                    <span class="badge badge-green">Bot respondiendo</span>
                    @endif
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
                    <svg style="width: 3rem; height: 3rem; margin: 0 auto; color: #9ca3af;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                    <h3 style="margin-top: 0.75rem; font-weight: 600;">No hay mensajes</h3>
                    <p style="font-size: 0.85rem;">La conversación comenzará cuando se envíe o reciba el primer mensaje.
                    </p>
                </div>
                @endforelse
            </div>

            {{-- Footer --}}
            @if($this->record->status === 'resuelto')
            <div class="chat-closed-banner">
                Esta conversación fue cerrada. El bot responderá automáticamente si el usuario envía un nuevo mensaje.
            </div>
            @elseif($this->canSendMessages())
            <div class="chat-footer">
                <div class="chat-input-group">
                    <input type="text" wire:model="newMessage" wire:keydown.enter="sendMessage"
                        placeholder="Escribe un mensaje..." class="chat-input" maxlength="4096">
                    <button wire:click="sendMessage" class="send-btn">
                        Enviar
                    </button>
                </div>
            </div>
            @else
            <div class="chat-closed-banner" style="background-color: #dbeafe; color: #1e40af;">
                Toma esta conversación para poder enviar mensajes.
            </div>
            @endif
        </div>
    </div>

    <script>
        function scrollToBottom() {
            var objDiv = document.getElementById("chat-messages-container");
            if (objDiv) objDiv.scrollTop = objDiv.scrollHeight;
        }
        window.addEventListener('load', scrollToBottom);
        document.addEventListener('livewire:navigated', scrollToBottom);
        document.addEventListener('livewire:morph.updated', scrollToBottom);
    </script>
</x-filament-panels::page>