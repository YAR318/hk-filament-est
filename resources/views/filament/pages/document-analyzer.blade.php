<x-filament-panels::page>
    <style>
        .da-page { max-width: 960px; margin: 0 auto; }

        /* Header */
        .da-header {
            border-radius: 1rem;
            padding: 2rem 2rem 1.75rem;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #ede9fe 0%, #dbeafe 50%, #d1fae5 100%);
            position: relative;
            overflow: hidden;
        }
        .dark .da-header {
            background: linear-gradient(135deg, #2e1065aa 0%, #1e3a5faa 50%, #064e3baa 100%);
            border-color: #374151;
        }
        .da-header h2 { font-size: 1.3rem; font-weight: 800; margin: 0 0 0.25rem; }
        .da-header p { font-size: 0.85rem; opacity: 0.6; margin: 0 0 1.25rem; }

        /* Upload zone */
        .da-upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
            margin-bottom: 1.5rem;
            position: relative;
        }
        .da-upload-zone:hover { border-color: #818cf8; background: #f5f3ff; }
        .dark .da-upload-zone { background: #1f2937; border-color: #4b5563; }
        .dark .da-upload-zone:hover { border-color: #818cf8; background: #1e1b4b44; }
        .da-upload-zone svg { width: 3rem; height: 3rem; opacity: 0.4; margin: 0 auto 0.75rem; }
        .da-upload-zone h3 { font-size: 1rem; font-weight: 700; margin: 0 0 0.25rem; }
        .da-upload-zone p { font-size: 0.8rem; opacity: 0.5; margin: 0; }
        .da-upload-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

        /* Cards */
        .da-card {
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            background-color: white;
        }
        .dark .da-card { background-color: #1f2937; border-color: #374151; }

        /* Stats */
        .da-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
        @media (max-width: 640px) { .da-stats { grid-template-columns: repeat(2, 1fr); } }
        .da-stat {
            border-radius: 0.75rem;
            padding: 0.85rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            text-align: center;
        }
        .dark .da-stat { background-color: #1f2937; border-color: #374151; }
        .da-stat-label { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; color: #6b7280; margin: 0 0 0.2rem; }
        .da-stat-value { font-size: 1rem; font-weight: 800; margin: 0; }

        /* File type badge */
        .da-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.7rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: white;
        }

        /* Summary card */
        .da-summary-card {
            border-radius: 0.75rem;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            border-left: 4px solid #818cf8;
        }
        .dark .da-summary-card { background-color: #1f2937; border-color: #374151; border-left-color: #818cf8; }
        .da-summary-card .prose { font-size: 0.9rem; line-height: 1.7; }

        /* Key points */
        .da-keypoints { display: grid; grid-template-columns: 1fr; gap: 0.5rem; margin-bottom: 1.5rem; }
        .da-keypoint {
            border-radius: 0.6rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            background: white;
            font-size: 0.85rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
        }
        .dark .da-keypoint { background: #1f2937; border-color: #374151; }
        .da-keypoint-icon {
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 50%;
            background: #818cf8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.65rem;
            font-weight: 800;
            margin-top: 0.1rem;
        }

        /* Section title */
        .da-section-title { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem; }

        /* Chat */
        .da-chat-container {
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            background: white;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .dark .da-chat-container { background: #1f2937; border-color: #374151; }
        .da-chat-header {
            padding: 0.85rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .dark .da-chat-header { border-color: #374151; }
        .da-chat-messages {
            padding: 1.25rem;
            max-height: 400px;
            overflow-y: auto;
            min-height: 120px;
        }
        .da-chat-empty {
            text-align: center;
            padding: 2rem;
            opacity: 0.4;
            font-size: 0.85rem;
        }
        .da-chat-msg {
            margin-bottom: 0.85rem;
            display: flex;
            gap: 0.6rem;
        }
        .da-chat-msg.user { justify-content: flex-end; }
        .da-chat-bubble {
            max-width: 80%;
            padding: 0.7rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            line-height: 1.6;
        }
        .da-chat-msg.user .da-chat-bubble {
            background: #818cf8;
            color: white;
            border-bottom-right-radius: 0.2rem;
        }
        .da-chat-msg.assistant .da-chat-bubble {
            background: #f3f4f6;
            border-bottom-left-radius: 0.2rem;
        }
        .dark .da-chat-msg.assistant .da-chat-bubble { background: #374151; }
        .da-chat-input-row {
            padding: 0.85rem 1.25rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 0.5rem;
        }
        .dark .da-chat-input-row { border-color: #374151; }
        .da-chat-input {
            flex: 1;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.5rem 0.85rem;
            font-size: 0.85rem;
            background: transparent;
            outline: none;
            color: inherit;
        }
        .da-chat-input:focus { border-color: #818cf8; box-shadow: 0 0 0 2px rgba(129,140,248,0.2); }
        .dark .da-chat-input { border-color: #4b5563; }
        .da-chat-send {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background: #818cf8;
            color: white;
            border: none;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background 0.15s;
        }
        .da-chat-send:hover { background: #6366f1; }
        .da-chat-send:disabled { opacity: 0.5; cursor: not-allowed; }

        /* History */
        .da-history-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem; }
        .da-history-item {
            border-radius: 0.75rem;
            padding: 0.9rem 1rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .da-history-item:hover { border-color: #818cf8; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .dark .da-history-item { background-color: #1f2937; border-color: #374151; }
        .dark .da-history-item:hover { border-color: #818cf8; }
        .da-history-item.active { border-color: #818cf8; background-color: #ede9fe; }
        .dark .da-history-item.active { background-color: #2e106544; border-color: #818cf8; }
        .da-history-info { flex: 1; min-width: 0; }
        .da-history-title { font-size: 0.82rem; font-weight: 700; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .da-history-meta { font-size: 0.68rem; color: #6b7280; margin: 0.15rem 0 0; }
        .da-history-delete {
            flex-shrink: 0;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 0.35rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: #9ca3af;
            cursor: pointer;
            transition: all 0.15s;
        }
        .da-history-delete:hover { background: #fee2e2; color: #ef4444; }
        .dark .da-history-delete:hover { background: #7f1d1d44; color: #f87171; }

        /* Spinner */
        .da-spinner { width: 2rem; height: 2rem; border: 3px solid #e5e7eb; border-top: 3px solid #818cf8; border-radius: 50%; animation: da-spin 0.8s linear infinite; margin: 0 auto 1rem; }
        @keyframes da-spin { to { transform: rotate(360deg); } }

        /* Clear button */
        .da-btn-sm {
            font-size: 0.7rem;
            padding: 0.25rem 0.6rem;
            border-radius: 0.35rem;
            border: 1px solid #d1d5db;
            background: transparent;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s;
        }
        .da-btn-sm:hover { border-color: #ef4444; color: #ef4444; }
        .dark .da-btn-sm { border-color: #4b5563; color: #9ca3af; }

        /* Chat prose */
        .da-chat-bubble .prose { font-size: 0.85rem; }
        .da-chat-bubble .prose p { margin: 0.25rem 0; }
    </style>

    <div class="da-page">

        {{-- HEADER --}}
        <div class="da-header">
            <h2>Analizador Inteligente de Documentos</h2>
            <p>Sube un PDF, Word o TXT para obtener resumenes, puntos clave y chatear con tu documento usando IA.</p>
        </div>

        {{-- UPLOAD ZONE --}}
        <div class="da-upload-zone"
             x-data="{ dragging: false }"
             x-on:dragover.prevent="dragging = true"
             x-on:dragleave="dragging = false"
             x-on:drop.prevent="dragging = false"
             :class="{ 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20': dragging }">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
            </svg>
            <h3>Arrastra un documento aqui</h3>
            <p>o haz clic para seleccionar — PDF, Word (.docx) o TXT — Max. 10MB</p>
            <input type="file" wire:model="uploadedFile" accept=".pdf,.docx,.doc,.txt" class="da-upload-input">
        </div>

        {{-- LOADING --}}
        <div wire:loading wire:target="uploadedFile, analyzeFile">
            <div class="da-card" style="text-align: center; padding: 3rem;">
                <div class="da-spinner"></div>
                <p style="font-weight: 700; margin: 0;">Analizando documento</p>
                <p style="font-size: 0.8rem; opacity: 0.5; margin: 0.25rem 0 0;">Extrayendo texto y generando resumen con IA...</p>
            </div>
        </div>

        {{-- RESULTS --}}
        @if($currentSummary)
            <div wire:loading.remove wire:target="uploadedFile, analyzeFile">

                {{-- Title & Stats --}}
                <div class="da-stats">
                    <div class="da-stat">
                        <p class="da-stat-label">Documento</p>
                        <p class="da-stat-value" style="font-size: 0.8rem; word-break: break-all;">{{ $currentTitle }}</p>
                    </div>
                    <div class="da-stat">
                        <p class="da-stat-label">Tipo</p>
                        <p class="da-stat-value">
                            <span class="da-badge" style="background: {{ match($currentFileType) { 'pdf' => '#ef4444', 'docx', 'doc' => '#3b82f6', default => '#22c55e' } }}">
                                {{ strtoupper($currentFileType) }}
                            </span>
                        </p>
                    </div>
                    <div class="da-stat">
                        <p class="da-stat-label">Tamano</p>
                        <p class="da-stat-value">{{ $currentFileSize }}</p>
                    </div>
                    <div class="da-stat">
                        <p class="da-stat-label">Caracteres</p>
                        <p class="da-stat-value">{{ number_format($currentTextLength) }}</p>
                    </div>
                </div>

                {{-- SUMMARY --}}
                <h3 class="da-section-title">Resumen Ejecutivo</h3>
                <div class="da-summary-card">
                    <div class="prose dark:prose-invert max-w-none prose-sm">
                        {!! \Illuminate\Support\Str::markdown($currentSummary) !!}
                    </div>
                </div>

                {{-- KEY POINTS --}}
                @if($currentKeyPoints && count($currentKeyPoints) > 0)
                    <h3 class="da-section-title">Puntos Clave</h3>
                    <div class="da-keypoints">
                        @foreach($currentKeyPoints as $i => $point)
                            <div class="da-keypoint">
                                <div class="da-keypoint-icon">{{ $i + 1 }}</div>
                                <span>{{ $point }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- CHAT WITH DOCUMENT --}}
                <div class="da-chat-container">
                    <div class="da-chat-header">
                        <span>Chat con el documento</span>
                        @if(count($chatMessages) > 0)
                            <button wire:click="clearChat" class="da-btn-sm">Limpiar chat</button>
                        @endif
                    </div>

                    <div class="da-chat-messages" id="chat-messages">
                        @if(count($chatMessages) === 0)
                            <div class="da-chat-empty">
                                Hazle una pregunta al documento. Ejemplo: "Cuales son las fechas mencionadas?" o "Resume la seccion de conclusiones."
                            </div>
                        @else
                            @foreach($chatMessages as $msg)
                                <div class="da-chat-msg {{ $msg['role'] }}">
                                    <div class="da-chat-bubble">
                                        @if($msg['role'] === 'assistant')
                                            <div class="prose dark:prose-invert max-w-none prose-sm">
                                                {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                                            </div>
                                        @else
                                            {{ $msg['content'] }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- Typing indicator --}}
                        <div wire:loading wire:target="sendChatMessage" class="da-chat-msg assistant">
                            <div class="da-chat-bubble" style="opacity: 0.6;">
                                Pensando...
                            </div>
                        </div>
                    </div>

                    <form wire:submit="sendChatMessage" class="da-chat-input-row">
                        <input type="text"
                               wire:model="chatQuestion"
                               class="da-chat-input"
                               placeholder="Escribe tu pregunta sobre el documento..."
                               autocomplete="off"
                               @disabled($isChatting)>
                        <button type="submit" class="da-chat-send" @disabled($isChatting || empty($chatQuestion))>
                            <span wire:loading.remove wire:target="sendChatMessage">Enviar</span>
                            <span wire:loading wire:target="sendChatMessage">...</span>
                        </button>
                    </form>
                </div>

            </div>
        @endif

        {{-- HISTORY --}}
        @if(count($this->history) > 0)
            <div style="margin-top: 2rem;">
                <h3 class="da-section-title">Documentos analizados</h3>
                <div class="da-history-grid">
                    @foreach($this->history as $doc)
                        <div class="da-history-item {{ $currentAnalysisId === $doc->id ? 'active' : '' }}">
                            <div class="da-history-info" wire:click="loadHistoric({{ $doc->id }})" style="cursor: pointer;">
                                <p class="da-history-title">{{ $doc->title }}</p>
                                <p class="da-history-meta">
                                    <span class="da-badge" style="background: {{ $doc->file_color }}; font-size: 0.55rem; padding: 0.1rem 0.4rem;">{{ strtoupper($doc->file_type) }}</span>
                                    &middot; {{ $doc->formatted_size }}
                                    &middot; {{ $doc->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <button wire:click="deleteAnalysis({{ $doc->id }})"
                                    wire:confirm="Eliminar este documento?"
                                    class="da-history-delete" title="Eliminar">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.85rem; height: 0.85rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Auto-scroll chat --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            Livewire.hook('morph.updated', ({el}) => {
                const chat = document.getElementById('chat-messages');
                if (chat) chat.scrollTop = chat.scrollHeight;
            });
        });
    </script>
</x-filament-panels::page>
