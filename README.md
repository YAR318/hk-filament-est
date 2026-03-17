# Proyecto: HK Filament + Auth + WhatsApp Bot

Este es el repo del proyecto para la estadía. Básicamente es un panel de administración hecho con **Filament** (Laravel) que incluye:
1.  **Autenticación**: Login con Google (OAuth) y correo/password normal.
2.  **WhatsApp Bot**: Integración con Evolution API y Groq IA para contestar mensajes automáticamente.

## Cómo levantarlo en local

Necesitas tener **Laravel Herd** instalado y corriendo.

1.  **Clonar el repo** (o descargar el zip).
2.  **Instalar dependencias**:
    ```bash
    composer install
    npm install && npm run build
    ```
3.  **Configurar base de datos**:
    - Crea una base de datos llamada `hk_autenticacion` en tu gestor (DBngin o el que uses).
    - Copia el `.env.example` a `.env` y configura la conexión:
      ```env
      DB_DATABASE=hk_autenticacion
      DB_USERNAME=root
      DB_PASSWORD=
      ```
    - Corre las migraciones:
      ```bash
      php artisan migrate --seed
      ```
4.  **Configurar Google Login**:
    - Necesitas las credenciales (`CLIENT_ID` y `SECRET`) de Google Cloud Console.
    - Ponlas en el `.env`:
      ```env
      GOOGLE_CLIENT_ID=tu_id_aqui
      GOOGLE_CLIENT_SECRET=tu_secreto_aqui
      GOOGLE_REDIRECT_URI=http://hk-filament.local.com/auth/google/callback
      ```

## Sobre el Bot de WhatsApp

El bot usa **n8n** (en Docker) para orquestar todo.
- **Workflow**: `n8n_workflow_evolution.json` (no está en el repo por seguridad, pedirmelo si lo ocupan).
- **IA**: Groq (Llama 3 / gpt-oss 120b).
- **WhatsApp API**: Evolution API.
- **WhatsApp API PROXIMAMENTE**: META API.

### Notas importantes del Bot:
- Los mensajes se guardan en la DB local (`whatsapp_messages` y `chat_conversations`).
- El historial se ve en el panel de admin > Conversaciones.
- Si vas a probar localmente, asegúrate de que n8n pueda ver Laravel (usar `host.docker.internal`).

## Estructura clave

- `app/Filament/`: Todo lo del panel de admin.
- `app/Services/ChatHistoryService.php`: La lógica para guardar y recuperar chats.
- `app/Http/Controllers/Auth/`: Lo del login con Google.
- `routes/api.php`: Endpoints para que n8n guarde los mensajes.

---
*Cualquier duda, el becario de la utj estuvo aqui.*
