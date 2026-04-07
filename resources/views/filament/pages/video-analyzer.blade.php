<x-filament-panels::page>
    <style>
        .va-page { max-width: 960px; margin: 0 auto; }

        /* Header */
        .va-header {
            border-radius: 1rem;
            padding: 2rem 2rem 1.75rem;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #fce7f3 0%, #ede9fe 50%, #dbeafe 100%);
            position: relative; overflow: hidden;
        }
        .dark .va-header {
            background: linear-gradient(135deg, #831843aa 0%, #2e1065aa 50%, #1e3a5faa 100%);
            border-color: #374151;
        }
        .va-header h2 { font-size: 1.3rem; font-weight: 800; margin: 0 0 0.25rem; }
        .va-header p { font-size: 0.85rem; opacity: 0.6; margin: 0 0 1.25rem; }

        /* Upload zone */
        .va-upload-zone {
            border: 2px dashed #cbd5e1; border-radius: 1rem; padding: 2.5rem 2rem;
            text-align: center; cursor: pointer; transition: all 0.2s;
            background: white; margin-bottom: 1.5rem; position: relative;
        }
        .va-upload-zone:hover { border-color: #c084fc; background: #faf5ff; }
        .dark .va-upload-zone { background: #1f2937; border-color: #4b5563; }
        .dark .va-upload-zone:hover { border-color: #c084fc; background: #1e1b4b44; }
        .va-upload-zone svg { width: 3rem; height: 3rem; opacity: 0.4; margin: 0 auto 0.75rem; }
        .va-upload-zone h3 { font-size: 1rem; font-weight: 700; margin: 0 0 0.25rem; }
        .va-upload-zone p { font-size: 0.8rem; opacity: 0.5; margin: 0; }
        .va-upload-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

        /* Cards */
        .va-card { border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1rem; border: 1px solid #e5e7eb; background-color: white; }
        .dark .va-card { background-color: #1f2937; border-color: #374151; }

        /* Stats */
        .va-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
        @media (max-width: 640px) { .va-stats { grid-template-columns: repeat(2, 1fr); } }
        .va-stat { border-radius: 0.75rem; padding: 0.85rem; border: 1px solid #e5e7eb; background-color: white; text-align: center; }
        .dark .va-stat { background-color: #1f2937; border-color: #374151; }
        .va-stat-label { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; color: #6b7280; margin: 0 0 0.2rem; }
        .va-stat-value { font-size: 1rem; font-weight: 800; margin: 0; }

        /* Processing status */
        .va-processing {
            border-radius: 0.75rem; padding: 2.5rem; text-align: center;
            border: 1px solid #e5e7eb; background: white; margin-bottom: 1.5rem;
        }
        .dark .va-processing { background: #1f2937; border-color: #374151; }
        .va-spinner { width: 2.5rem; height: 2.5rem; border: 3px solid #e5e7eb; border-top: 3px solid #c084fc; border-radius: 50%; animation: va-spin 0.8s linear infinite; margin: 0 auto 1rem; }
        @keyframes va-spin { to { transform: rotate(360deg); } }

        .va-status-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.3rem 0.85rem; border-radius: 9999px;
            font-size: 0.75rem; font-weight: 700;
        }

        /* Steps progress */
        .va-steps { display: flex; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; flex-wrap: wrap; }
        .va-step {
            display: flex; align-items: center; gap: 0.3rem;
            padding: 0.35rem 0.7rem; border-radius: 9999px;
            font-size: 0.7rem; font-weight: 600;
            border: 1px solid #e5e7eb; background: white;
            color: #9ca3af;
        }
        .dark .va-step { background: #1f2937; border-color: #374151; }
        .va-step.active { border-color: #c084fc; color: #c084fc; background: #faf5ff; }
        .dark .va-step.active { background: #581c8744; }
        .va-step.done { border-color: #22c55e; color: #22c55e; background: #f0fdf4; }
        .dark .va-step.done { background: #064e3b44; }
        .va-step-dot { width: 0.5rem; height: 0.5rem; border-radius: 50%; background: currentColor; }

        /* Section */
        .va-section-title { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem; }

        /* Summary card */
        .va-summary-card {
            border-radius: 0.75rem; padding: 1.5rem 2rem; margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb; background-color: white; border-left: 4px solid #c084fc;
        }
        .dark .va-summary-card { background-color: #1f2937; border-color: #374151; border-left-color: #c084fc; }

        /* Decisions */
        .va-decision {
            border-radius: 0.6rem; padding: 0.75rem 1rem; border: 1px solid #e5e7eb;
            background: white; font-size: 0.85rem; display: flex; align-items: flex-start; gap: 0.6rem;
            margin-bottom: 0.5rem;
        }
        .dark .va-decision { background: #1f2937; border-color: #374151; }
        .va-decision-icon {
            flex-shrink: 0; width: 1.25rem; height: 1.25rem;
            border-radius: 50%; background: #f59e0b; display: flex;
            align-items: center; justify-content: center; color: white;
            font-size: 0.6rem; font-weight: 800; margin-top: 0.1rem;
        }

        /* Tasks */
        .va-task {
            border-radius: 0.6rem; padding: 0.75rem 1rem; border: 1px solid #e5e7eb;
            background: white; font-size: 0.85rem; display: flex; align-items: center; gap: 0.75rem;
            margin-bottom: 0.5rem; cursor: pointer; transition: all 0.15s;
        }
        .va-task:hover { border-color: #c084fc; }
        .dark .va-task { background: #1f2937; border-color: #374151; }
        .dark .va-task:hover { border-color: #c084fc; }
        .va-task.done { opacity: 0.5; }
        .va-task.done .va-task-text { text-decoration: line-through; }
        .va-task-check {
            flex-shrink: 0; width: 1.4rem; height: 1.4rem; border-radius: 0.35rem;
            border: 2px solid #d1d5db; display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
        }
        .va-task.done .va-task-check { background: #22c55e; border-color: #22c55e; }
        .va-task-info { flex: 1; }
        .va-task-text { font-weight: 600; margin: 0; }
        .va-task-assignee { font-size: 0.72rem; color: #6b7280; margin: 0.1rem 0 0; }

        /* Transcription toggle */
        .va-detail-toggle {
            display: inline-flex; align-items: center; gap: 0.35rem;
            font-size: 0.8rem; font-weight: 600; color: #6b7280;
            background: none; border: none; cursor: pointer; padding: 0.5rem 0; transition: color 0.15s;
        }
        .va-detail-toggle:hover { color: #c084fc; }
        .va-detail-toggle svg { width: 1rem; height: 1rem; transition: transform 0.15s; }
        .va-transcription-text { font-size: 0.85rem; line-height: 1.8; white-space: pre-wrap; }

        /* History */
        .va-history-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem; }
        .va-history-item {
            border-radius: 0.75rem; padding: 0.9rem 1rem; border: 1px solid #e5e7eb;
            background-color: white; cursor: pointer; transition: all 0.15s;
            display: flex; align-items: center; gap: 0.75rem;
        }
        .va-history-item:hover { border-color: #c084fc; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .dark .va-history-item { background-color: #1f2937; border-color: #374151; }
        .dark .va-history-item:hover { border-color: #c084fc; }
        .va-history-item.active { border-color: #c084fc; background-color: #faf5ff; }
        .dark .va-history-item.active { background-color: #581c8744; border-color: #c084fc; }
        .va-history-info { flex: 1; min-width: 0; }
        .va-history-title { font-size: 0.82rem; font-weight: 700; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .va-history-meta { font-size: 0.68rem; color: #6b7280; margin: 0.15rem 0 0; }
        .va-history-delete {
            flex-shrink: 0; width: 1.5rem; height: 1.5rem; border-radius: 0.35rem;
            display: flex; align-items: center; justify-content: center;
            border: none; background: transparent; color: #9ca3af; cursor: pointer; transition: all 0.15s;
        }
        .va-history-delete:hover { background: #fee2e2; color: #ef4444; }
        .dark .va-history-delete:hover { background: #7f1d1d44; color: #f87171; }

        /* Error card */
        .va-error { border-left: 4px solid #ef4444; }
    </style>

    <div class="va-page">

        {{-- HEADER --}}
        <div class="va-header">
            <h2>Analizador de Reuniones y Videos</h2>
            <p>Sube la grabacion de una reunion (MP4, WebM, MOV) o un audio (MP3, WAV) y obtendras minutas, decisiones y tareas automaticamente.</p>
        </div>

        {{-- UPLOAD ZONE --}}
        <div class="va-upload-zone"
             x-data="{ dragging: false }"
             x-on:dragover.prevent="dragging = true"
             x-on:dragleave="dragging = false"
             x-on:drop.prevent="dragging = false"
             :class="{ 'border-purple-400 bg-purple-50 dark:bg-purple-900/20': dragging }">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h3>Arrastra un video o audio aqui</h3>
            <p>o haz clic para seleccionar — MP4, WebM, MOV, MP3, WAV — Max. 500MB</p>
            <input type="file" wire:model="uploadedVideo" accept=".mp4,.webm,.mov,.avi,.mkv,.mp3,.wav,.ogg,.m4a" class="va-upload-input">
        </div>

        {{-- UPLOADING --}}
        <div wire:loading wire:target="uploadedVideo">
            <div class="va-processing">
                <div class="va-spinner"></div>
                <p style="font-weight: 700; margin: 0;">Subiendo archivo</p>
                <p style="font-size: 0.8rem; opacity: 0.5; margin: 0.25rem 0 0;">Esto puede tomar unos segundos dependiendo del tamano del archivo...</p>
            </div>
        </div>

        {{-- PROCESSING STATUS --}}
        @if($currentStatus && $currentStatus !== 'completed' && $currentStatus !== 'failed')
            <div wire:poll.3s="refreshStatus">
                <div class="va-processing">
                    <div class="va-spinner"></div>
                    <p style="font-weight: 700; margin: 0 0 0.5rem;">{{ $currentTitle }}</p>
                    <div class="va-status-badge" style="background: #faf5ff; color: #a855f7;">
                        <span class="va-step-dot"></span>
                        {{ $currentStatusLabel }}
                    </div>

                    <div class="va-steps">
                        @php
                            $steps = ['uploading', 'extracting_audio', 'transcribing', 'analyzing', 'completed'];
                            $currentIdx = array_search($currentStatus, $steps);
                        @endphp
                        @foreach(['Subida', 'Extraccion Audio', 'Transcripcion', 'Analisis IA', 'Listo'] as $i => $label)
                            <div class="va-step {{ $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'active' : '') }}">
                                <span class="va-step-dot"></span>
                                {{ $label }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ERROR --}}
        @if($currentStatus === 'failed')
            <div class="va-card va-error">
                <p style="font-weight: 700; color: #ef4444; margin: 0 0 0.25rem;">Error al procesar el video</p>
                <p style="font-size: 0.85rem; margin: 0;">{{ $currentTitle }}: {{ $currentTranscription ?? 'Error desconocido' }}</p>
            </div>
        @endif

        {{-- RESULTS --}}
        @if($currentStatus === 'completed' && $currentSummary)

            {{-- Stats --}}
            <div class="va-stats">
                <div class="va-stat">
                    <p class="va-stat-label">Reunion</p>
                    <p class="va-stat-value" style="font-size: 0.8rem;">{{ $currentTitle }}</p>
                </div>
                <div class="va-stat">
                    <p class="va-stat-label">Duracion</p>
                    <p class="va-stat-value">{{ $currentDuration }}</p>
                </div>
                <div class="va-stat">
                    <p class="va-stat-label">Tamano</p>
                    <p class="va-stat-value">{{ $currentFileSize }}</p>
                </div>
                <div class="va-stat">
                    <p class="va-stat-label">Formato</p>
                    <p class="va-stat-value">{{ strtoupper($currentFileType) }}</p>
                </div>
            </div>

            {{-- SUMMARY --}}
            <h3 class="va-section-title">Resumen de la Reunion</h3>
            <div class="va-summary-card">
                <div class="prose dark:prose-invert max-w-none prose-sm">
                    {!! \Illuminate\Support\Str::markdown($currentSummary) !!}
                </div>
            </div>

            {{-- DECISIONS --}}
            @if($currentDecisions && count($currentDecisions) > 0)
                <h3 class="va-section-title">Decisiones Tomadas</h3>
                @foreach($currentDecisions as $i => $decision)
                    <div class="va-decision">
                        <div class="va-decision-icon">{{ $i + 1 }}</div>
                        <span>{{ $decision }}</span>
                    </div>
                @endforeach
                <div style="margin-bottom: 1.5rem;"></div>
            @endif

            {{-- TASKS --}}
            @if($currentTasks && count($currentTasks) > 0)
                <h3 class="va-section-title">Tareas y Compromisos ({{ collect($currentTasks)->where('done', true)->count() }}/{{ count($currentTasks) }})</h3>
                @foreach($currentTasks as $i => $task)
                    <div class="va-task {{ ($task['done'] ?? false) ? 'done' : '' }}" wire:click="toggleTask({{ $i }})">
                        <div class="va-task-check">
                            @if($task['done'] ?? false)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="white" style="width: 0.8rem; height: 0.8rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            @endif
                        </div>
                        <div class="va-task-info">
                            <p class="va-task-text">{{ $task['task'] }}</p>
                            @if(!empty($task['assignee']) && $task['assignee'] !== 'Sin asignar')
                                <p class="va-task-assignee">Responsable: {{ $task['assignee'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
                <div style="margin-bottom: 1.5rem;"></div>
            @endif

            {{-- TRANSCRIPTION --}}
            @if($currentTranscription)
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="va-detail-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :style="open ? 'transform: rotate(90deg)' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                        Ver transcripcion completa
                    </button>

                    <div x-show="open" x-collapse>
                        <div class="va-card" style="margin-top: 0.5rem;">
                            <p class="va-transcription-text">{{ $currentTranscription }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- HISTORY --}}
        @if(count($this->history) > 0)
            <div style="margin-top: 2rem;">
                <h3 class="va-section-title">Videos analizados</h3>
                <div class="va-history-grid">
                    @foreach($this->history as $video)
                        <div class="va-history-item {{ $currentAnalysisId === $video->id ? 'active' : '' }}">
                            <div class="va-history-info" wire:click="loadHistoric({{ $video->id }})" style="cursor: pointer;">
                                <p class="va-history-title">{{ $video->title }}</p>
                                <p class="va-history-meta">
                                    <span class="va-status-badge" style="background: {{ $video->status_color }}22; color: {{ $video->status_color }}; font-size: 0.55rem; padding: 0.1rem 0.4rem; border-radius: 9999px;">
                                        {{ $video->status_label }}
                                    </span>
                                    &middot; {{ $video->formatted_size }}
                                    &middot; {{ $video->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <button wire:click="deleteAnalysis({{ $video->id }})"
                                    wire:confirm="Eliminar este video?"
                                    class="va-history-delete" title="Eliminar">
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
</x-filament-panels::page>
