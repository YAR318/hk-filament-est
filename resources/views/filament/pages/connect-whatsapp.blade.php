<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Columna Izquierda: Pasos --}}
        <div class="space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    Pasos para conectar
                </x-slot>

                <div class="prose dark:prose-invert max-w-none">
                    <ol class="list-decimal pl-5 space-y-4">
                        <li>
                            <strong>Accede a la API de Evolution:</strong>
                            <p class="text-sm text-gray-500">Ingresa al panel de administración de Evolution API.</p>
                        </li>
                        <li>
                            <strong>Crear Instancia:</strong>
                            <p class="text-sm text-gray-500">Crea una nueva instancia con el nombre
                                <code>{{ config('app.name') }}</code>.</p>
                        </li>
                        <li>
                            <strong>Escanear QR:</strong>
                            <p class="text-sm text-gray-500">Abre Whatsapp en tu celular, ve a <em>Dispositivos
                                    vinculados</em> y escanea el código QR que muestra la API.</p>
                        </li>
                        <li>
                            <strong>Verificar Conexión:</strong>
                            <p class="text-sm text-gray-500">Una vez escaneado, el estado debería cambiar a
                                <code>open</code>.</p>
                        </li>
                    </ol>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Estado del Servicio
                </x-slot>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="font-medium text-green-600 dark:text-green-400">Sistema Operativo</span>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Columna Derecha: Información Técnica --}}
        <div class="space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    Información Técnica
                </x-slot>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Webhook URL</label>
                        <div class="mt-1 p-2 bg-gray-100 dark:bg-gray-800 rounded text-sm font-mono break-all group relative cursor-pointer"
                            onclick="navigator.clipboard.writeText(this.innerText); new Filament.Notification().title('Copiado').success().send()">
                            {{ config('app.url') }}/api/whatsapp/webhook
                            <span
                                class="absolute right-2 top-2 text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition">Copiar</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Configura esta URL en Evolution API para recibir mensajes.
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Events requeridos</label>
                        <ul class="mt-1 list-disc pl-5 text-sm text-gray-600 dark:text-gray-300">
                            <li>MESSAGES_UPSERT</li>
                            <li>MESSAGES_UPDATE</li>
                            <li>SEND_MESSAGE</li>
                        </ul>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    ¿Necesitas ayuda?
                </x-slot>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Si tienes problemas con la conexión, contacta al administrador del sistema.
                </p>
                <div class="mt-4">
                    <x-filament::button href="https://docs.evolution-api.com/" tag="a" target="_blank" color="gray">
                        Ver Documentación Oficial
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>

    </div>
</x-filament-panels::page>