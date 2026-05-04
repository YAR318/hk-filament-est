# HK Filament - Panel de Administracion con IA

Panel de administracion construido con **Laravel 12 + Filament v4**, que integra un bot de WhatsApp con IA, sistema de escalacion humana, y resumen inteligente de correos con inteligencia artificial.

---

## Tabla de Contenidos

1. [Requisitos del sistema](#requisitos-del-sistema)
2. [Instalacion rapida](#instalacion-rapida)
3. [Configuracion de credenciales (.env)](#configuracion-de-credenciales-env)
4. [Servicios y APIs externas](#servicios-y-apis-externas)
5. [Estructura del proyecto](#estructura-del-proyecto)
6. [Funcionalidades principales](#funcionalidades-principales)

---

## Requisitos del sistema

- **Docker** y **Docker Compose**
- **PHP 8.2+** (incluido en el contenedor)
- **MySQL 8.0+** (incluido en el contenedor)
- **Node.js 18+** (para compilar assets)
- **Composer 2+**

### Contenedores Docker del proyecto

| Contenedor | Funcion | Puerto |
|------------|---------|--------|
| `hk-filament` | Aplicacion Laravel / Filament | 80 |
| `hk-mysql` | Base de datos MySQL | 3306 |
| `hk-redis` | Cache y sesiones | 6379 |
| `hk-autenticacion` | Servidor SSO | 8001 |
| `n8n` | Automatizacion de workflows | 5678 |
| `evolution_api` | API de WhatsApp | 8080 |
| `evolution_manager` | Manager visual de Evolution | 8081 |
| `hk-postgres` | BD de n8n / Evolution | 5432 |

---

## Instalacion rapida

Este proyecto se entrega **completamente dockerizado**. Solo necesitas Docker instalado.

```bash
# 1. Clonar el repositorio
git clone https://github.com/YAR318/hk-filament-est.git
cd hk-filament-est

# 2. Copiar archivo de configuracion
cp .env.example .env
# (Editar .env con las credenciales - ver seccion siguiente)

# 3. Levantar todos los contenedores
docker compose up -d

# 4. Instalar dependencias dentro del contenedor
docker exec hk-filament composer install

# 5. Generar clave de aplicacion
docker exec hk-filament php artisan key:generate

# 6. Compilar assets frontend
docker exec hk-filament npm install && docker exec hk-filament npm run build

# 7. Ejecutar migraciones
docker exec hk-filament php artisan migrate --seed
```

> **NOTA:** Todos los comandos `php artisan` deben ejecutarse dentro del contenedor `hk-filament`.
> Puedes entrar al contenedor con: `docker exec -it hk-filament bash`

---

## Configuracion de credenciales (.env)

A continuacion se documenta **cada variable** que necesitas configurar. El archivo `.env` se divide en secciones.

---

### 1. Aplicacion General

```env
APP_NAME=HK_Filament
APP_ENV=production          # Cambiar a 'production' en servidor
APP_KEY=                    # Se genera con: php artisan key:generate
APP_DEBUG=false             # IMPORTANTE: false en produccion
APP_URL=https://tu-dominio.com   # URL real del servidor
APP_TIMEZONE="America/Mexico_City"
```

> **Notas:**
> - `APP_URL` debe coincidir con la URL real donde se accede al panel.
> - `APP_DEBUG=false` es obligatorio en produccion para no exponer errores.
> - `APP_KEY` se genera automaticamente con `php artisan key:generate`.

---

### 2. Base de Datos (MySQL)

```env
DB_CONNECTION=mysql
DB_HOST=mysql              # Nombre del contenedor Docker. Si no usas Docker, pon 127.0.0.1
DB_PORT=3306
DB_DATABASE=hk_autenticacion
DB_USERNAME=tu_usuario     # Usuario de MySQL
DB_PASSWORD=tu_password    # Password de MySQL
```

> **Notas:**
> - Si usas Docker, `DB_HOST` debe ser el nombre del servicio/contenedor (ej: `mysql` o `hk-mysql`).
> - Si instalas MySQL directamente en el servidor, usa `127.0.0.1` o `localhost`.
> - Despues de configurar, ejecuta: `php artisan migrate --seed` para crear las tablas.

---

### 3. Sesiones y Cache

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.tu-dominio.com   # Tu dominio SIN protocolo
SESSION_COOKIE=hunabku_session

CACHE_STORE=database
QUEUE_CONNECTION=database
```

> **Notas:**
> - `SESSION_DOMAIN` debe ser tu dominio con un punto al inicio (ej: `.miempresa.com`).
> - Si tienes Redis disponible, puedes cambiar `CACHE_STORE=redis` y `SESSION_DRIVER=redis` para mejor rendimiento.

---

### 4. Redis (opcional)

```env
REDIS_CLIENT=phpredis
REDIS_HOST=redis           # Nombre del contenedor, o 127.0.0.1 si es local
REDIS_PASSWORD=null        # Pon tu password si Redis lo tiene
REDIS_PORT=6379
```

---

### 5. Correo SMTP (para notificaciones por email)

El sistema envia emails de notificacion. Se recomienda usar Gmail con una **Contrasena de Aplicacion**.

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME="tu-correo@gmail.com"
MAIL_PASSWORD="xxxx xxxx xxxx xxxx"    # Contrasena de aplicacion de 16 caracteres
MAIL_ENCRYPTION=smtps
MAIL_FROM_ADDRESS="tu-correo@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Como obtener la Contrasena de Aplicacion de Gmail:

1. Ve a [https://myaccount.google.com/security](https://myaccount.google.com/security)
2. Activa la **verificacion en dos pasos** si no la tienes.
3. Busca **"Contrasenas de aplicaciones"** (o ve directo a [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)).
4. Crea una nueva contrasena para "Correo" > "Otro" > nombra "HK Filament".
5. Google te dara una clave de 16 caracteres (ej: `abcd efgh ijkl mnop`). Esa va en `MAIL_PASSWORD`.

> **IMPORTANTE:** Esta misma contrasena de aplicacion se reutiliza en la seccion de IMAP (punto 9).

---

### 6. Google OAuth (Login con Google)

Permite a los usuarios iniciar sesion con su cuenta de Google.

```env
GOOGLE_CLIENT_ID=tu_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=https://tu-dominio.com/auth/google/callback
```

#### Como obtener las credenciales:

1. Ve a [Google Cloud Console](https://console.cloud.google.com/).
2. Crea un proyecto nuevo o selecciona uno existente.
3. Ve a **APIs y Servicios > Credenciales**.
4. Crea credenciales tipo **"ID de cliente OAuth 2.0"**.
5. Tipo de aplicacion: **"Aplicacion web"**.
6. En **"URIs de redireccionamiento autorizados"**, agrega: `https://tu-dominio.com/auth/google/callback`
7. Copia el `Client ID` y `Client Secret` al `.env`.

> **IMPORTANTE:** La URL de `GOOGLE_REDIRECT_URI` debe coincidir EXACTAMENTE con la registrada en Google Cloud Console, incluyendo protocolo (http/https).

---

### 7. Evolution API (WhatsApp Bot - canal Evolution)

Evolution API gestiona la conexion con WhatsApp via QR (no oficial).

```env
EVOLUTION_BASE_URL=http://evolution_api:8080     # URL del contenedor Evolution
EVOLUTION_API_KEY=tu_api_key_de_evolution        # API Key configurada en Evolution
```

> **Notas:**
> - Si Evolution API corre en Docker junto con la app, usa el nombre del contenedor (`evolution_api`).
> - Si corre en otro servidor, pon la URL completa (ej: `https://evolution.miempresa.com`).
> - La `EVOLUTION_API_KEY` se configura en el `.env` del contenedor de Evolution API.

---

### 8. Meta WhatsApp Cloud API (WhatsApp Bot - canal oficial)

El sistema soporta **dos canales de WhatsApp**: Evolution API (punto 7) y la **API oficial de Meta**.
Las credenciales de Meta **NO van en el `.env`**, sino que se configuran desde el panel de Filament.

#### Paso 1: Crear la App en Meta

1. Ve a [Meta for Developers](https://developers.facebook.com/) e inicia sesion con tu cuenta de Facebook.
2. Click en **"Mis Apps"** (esquina superior derecha) > **"Crear app"**.
3. Selecciona tipo de app: **"Negocio"** (o "Otro" si no aparece Negocio).
4. Llena el nombre de la App (ej: "HK WhatsApp Bot") y selecciona tu Business Portfolio. Click **"Crear app"**.
5. En el dashboard de tu App, busca el producto **"WhatsApp"** y click en **"Configurar"**.

#### Paso 2: Obtener el Phone Number ID

1. En el menu lateral izquierdo, ve a **WhatsApp > Configuracion de la API** (o "API Setup").
2. Veras una seccion **"From"** con un numero de telefono de prueba que Meta te asigna automaticamente.
3. Debajo del numero veras el **Phone Number ID** (un numero largo, ej: `123456789012345`). **Copia este ID**, lo necesitaras despues.

> **NOTA:** Este es un numero de prueba. Para produccion, necesitas registrar tu propio numero de telefono en la seccion WhatsApp > Numeros de telefono.

#### Paso 3: Obtener el Access Token permanente

Meta te da un token **temporal** que expira en 24 horas. Para obtener uno **permanente**, sigue estos pasos:

1. Ve a [Meta Business Suite](https://business.facebook.com/) > **Configuracion del negocio** (Business Settings).
2. En el menu lateral izquierdo, navega a **Usuarios > Usuarios del sistema** (System Users).
3. Click en **"Agregar"** para crear un nuevo System User:
   - **Nombre:** `hk-whatsapp-bot` (o el nombre que quieras).
   - **Rol:** Selecciona **"Admin"**.
   - Click en **"Crear usuario del sistema"**.
4. Ahora necesitas **asignar recursos** al System User:
   - Click en el System User que acabas de crear.
   - Click en **"Asignar activos"** (Add Assets).
   - Selecciona la pestana **"Apps"**.
   - Busca tu App (la que creaste en el Paso 1) y seleccionala.
   - Activa el permiso **"Control total"** (Full Control).
   - Click en **"Guardar cambios"**.
5. Ahora genera el **token permanente**:
   - En la misma pagina del System User, click en **"Generar token"** (Generate Token).
   - Selecciona la App que creaste.
   - En la lista de permisos, marca estos dos:
     - `whatsapp_business_messaging`
     - `whatsapp_business_management`
   - Click en **"Generar token"**.
   - **COPIA EL TOKEN INMEDIATAMENTE** (empieza con `EAAG...`). Meta solo te lo muestra una vez.

> **IMPORTANTE:** Si pierdes el token, tendras que generar uno nuevo repitiendo el paso 5.

#### Paso 4: Configurar el Webhook

1. En [Meta for Developers](https://developers.facebook.com/), ve a tu App > Menu lateral > **WhatsApp > Configuracion** (o "Configuration").
2. En la seccion **"Webhook"**, click en **"Editar"**:
   - **URL de callback**: `https://tu-dominio.com/api/meta/webhook`
   - **Verify Token**: Inventa una cadena secreta (ej: `mi_token_secreto_123`). **Anota esta cadena**, la necesitaras en el panel de Filament.
3. Click en **"Verificar y guardar"**. Meta enviara una peticion GET a tu servidor para verificar el webhook.
4. Despues de verificar, en la seccion **"Campos de webhook"**, busca **"messages"** y click en **"Suscribirse"**.

> **NOTA:** Tu servidor debe ser accesible publicamente con HTTPS para que Meta pueda verificar el webhook. Si estas en desarrollo local, puedes usar [ngrok](https://ngrok.com/) para exponer tu servidor temporalmente.

#### Paso 5: Configurar en el panel de Filament

1. Inicia sesion en el panel de administracion.
2. Ve a **Configuracion** en el menu lateral (pagina de AppSettings).
3. En la seccion **"Meta WhatsApp"**, llena los campos:
   - **Phone Number ID**: El ID que copiaste de Meta (ej: `123456789012345`).
   - **Access Token (permanente)**: El token que empieza con `EAAG...`.
   - **Verify Token**: La misma cadena que pusiste en el webhook de Meta.
4. Guarda la configuracion.

> **IMPORTANTE:**
> - Estas credenciales se guardan en la base de datos (tabla `app_settings`), no en archivos.
> - El webhook de Meta es: `https://tu-dominio.com/api/meta/webhook` (debe ser HTTPS en produccion).
> - Meta requiere que tu servidor sea accesible publicamente con HTTPS para verificar el webhook.

---

### 9. Google Calendar (Agendamiento de citas via bot)

El bot puede agendar citas en Google Calendar.

```env
GOOGLE_CALENDAR_ID=primary
```

Ademas, necesitas un archivo de credenciales de servicio:

1. En [Google Cloud Console](https://console.cloud.google.com/), ve a **APIs y Servicios > Credenciales**.
2. Crea una **Cuenta de servicio**.
3. Descarga el archivo JSON de credenciales.
4. Guardalo en `storage/app/google-calendar-credentials.json` dentro del proyecto.
5. Comparte tu calendario de Google con el email de la cuenta de servicio (el email termina en `@...iam.gserviceaccount.com`).

---

### 10. IMAP Gmail (Resumen Inteligente de Correos con IA)

Esta funcion lee los correos del dia via IMAP y genera un resumen con IA.

```env
IMAP_HOST=imap.gmail.com
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=true
IMAP_USERNAME=tu-correo@gmail.com          # El correo que quieres leer
IMAP_PASSWORD=xxxx xxxx xxxx xxxx          # Misma contrasena de aplicacion del punto 5
```

#### Requisitos:

1. **Habilitar IMAP en Gmail:**
   - Abre Gmail > Configuracion (engranaje) > Ver toda la configuracion.
   - Pestana **"Reenvio y correo POP/IMAP"**.
   - En la seccion IMAP, selecciona **"Habilitar IMAP"** y guarda.

2. **Usar la misma Contrasena de Aplicacion** que generaste en el punto 5.

> **NOTA:** `IMAP_USERNAME` e `IMAP_PASSWORD` pueden ser diferentes a los de SMTP si quieres leer correos de una cuenta distinta a la que envia emails.

---

### 11. Groq API (Motor de IA)

Groq se usa como motor de inteligencia artificial para:
- Resumenes de correos (Email Digest)
- Bot de WhatsApp (a traves de n8n)

```env
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxxxxx
```

#### Como obtener la API Key:

1. Ve a [https://console.groq.com/keys](https://console.groq.com/keys).
2. Crea una cuenta si no tienes.
3. Genera una nueva API Key.
4. Copia la clave (empieza con `gsk_`).

> **NOTA:** La misma clave se usa tambien en n8n como credencial "Header Auth" para el nodo de Groq.

---

### 12. Servidor de Autenticacion SSO (opcional)

Si usas el sistema de autenticacion centralizado:

```env
AUTH_SERVER_URL=http://hk-autenticacion:8001   # URL del servidor SSO
```

> Solo necesario si el proyecto usa el microservicio de autenticacion separado.

---

### 13. n8n (Automatizacion / Cerebro del Bot)

n8n no se configura desde el `.env` de Laravel, sino desde su propio panel:

1. Accede a n8n en `http://tu-servidor:5678`.
2. Importa el workflow `WhatsApp Bot.json` que esta en la raiz del proyecto.
3. Configura estas credenciales dentro de n8n:
   - **Header Auth (Groq):** Tu API Key de Groq como header `Authorization: Bearer gsk_xxx`.
   - **Webhook URL:** Apunta a `http://hk-filament:80/api/whatsapp/webhook` (o la URL de tu servidor).

---

## Resumen rapido de variables .env

| Variable | Que es | Donde obtenerla |
|----------|--------|-----------------|
| `APP_KEY` | Clave de encriptacion | `php artisan key:generate` |
| `DB_USERNAME` / `DB_PASSWORD` | Credenciales MySQL | Tu servidor de BD |
| `MAIL_USERNAME` / `MAIL_PASSWORD` | Email SMTP | Gmail > Contrasenas de aplicacion |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` | Login con Google | Google Cloud Console > OAuth |
| `GOOGLE_REDIRECT_URI` | Callback OAuth | Tu URL + `/auth/google/callback` |
| `EVOLUTION_API_KEY` | WhatsApp API | .env de Evolution API |
| `EVOLUTION_BASE_URL` | URL de Evolution | URL del contenedor/servidor |
| `IMAP_USERNAME` / `IMAP_PASSWORD` | Lector de correos | Gmail + Contrasena de app |
| `GROQ_API_KEY` | Motor de IA | console.groq.com |
| `SESSION_DOMAIN` | Dominio de cookies | Tu dominio con punto inicial |
| **Meta WhatsApp** | **Se configura desde el panel** | **Meta for Developers + Filament > Configuracion** |

---

## Servicios y APIs externas

| Servicio | Para que se usa | Documentacion |
|----------|----------------|---------------|
| **Gmail SMTP** | Enviar emails de notificacion | [Google SMTP](https://support.google.com/mail/answer/7126229) |
| **Gmail IMAP** | Leer correos para el resumen con IA | [Google IMAP](https://support.google.com/mail/answer/7126229) |
| **Google OAuth** | Login con cuenta de Google | [Google OAuth 2.0](https://developers.google.com/identity/protocols/oauth2) |
| **Google Calendar** | Agendar citas via bot | [Google Calendar API](https://developers.google.com/calendar) |
| **Groq** | Inteligencia artificial (LLM) | [Groq Console](https://console.groq.com) |
| **Evolution API** | WhatsApp via QR (no oficial) | [Evolution API Docs](https://doc.evolution-api.com) |
| **Meta Cloud API** | WhatsApp oficial (API de Meta) | [Meta WhatsApp Docs](https://developers.facebook.com/docs/whatsapp/cloud-api) |
| **n8n** | Automatizacion de workflows | [n8n Docs](https://docs.n8n.io) |

---

## Estructura del proyecto

```
hk-filament-est/
├── app/
│   ├── Filament/
│   │   ├── Pages/
│   │   │   ├── Auth/Login.php              # Login personalizado
│   │   │   ├── ConnectWhatsapp.php         # Conexion WhatsApp + QR
│   │   │   └── EmailDigestPage.php         # Resumen IA de correos
│   │   ├── Resources/                      # CRUD de Filament (Usuarios, Roles, etc.)
│   │   └── Responses/LoginResponse.php     # Redireccion post-login por rol
│   ├── Http/Controllers/
│   │   ├── Api/ChatHistoryController.php   # API para mensajes y escalacion
│   │   └── Auth/                           # Controllers de Google OAuth
│   ├── Models/
│   │   ├── EmailDigest.php                 # Modelo de resumenes de correo
│   │   ├── ChatConversation.php            # Conversaciones de WhatsApp
│   │   └── Operator.php                    # Operadores para escalacion
│   ├── Services/
│   │   ├── EmailDigestService.php          # IMAP + Groq AI
│   │   ├── ChatHistoryService.php          # Historial de chats
│   │   ├── EvolutionService.php            # Evolution API client
│   │   └── GoogleCalendarService.php       # Google Calendar
│   └── Livewire/
│       └── AudioNotifier.php               # Notificacion sonora para operadores
├── config/
│   └── services.php                        # Todas las APIs configuradas
├── database/migrations/                    # Migraciones de BD
├── resources/views/
│   ├── filament/pages/                     # Vistas Blade personalizadas
│   └── livewire/                           # Componentes Livewire
├── routes/
│   ├── api.php                             # Endpoints REST
│   └── web.php                             # Rutas web
├── WhatsApp Bot.json                       # Workflow de n8n (importar en n8n)
└── .env                                    # Configuracion (NO subir a git)
```

---

## Funcionalidades principales

### 1. Panel de Administracion (Filament)
- CRUD de usuarios, roles y permisos (Spatie Permission).
- Login con email/password y Google OAuth.
- Redireccion por rol (admin va a `/admin`, usuario normal a `/profile`).

### 2. Bot de WhatsApp con IA
- Respuestas automaticas con Groq AI (Llama 3.3 70B).
- Agendamiento de citas en Google Calendar.
- Escalacion a operador humano cuando el usuario lo solicita.
- Notificaciones en tiempo real al operador asignado (con sonido).
- Historial de conversaciones en el panel.

### 3. Resumen Inteligente de Correos
- Conecta a Gmail via IMAP.
- Lee todos los correos del dia.
- Genera un resumen ejecutivo con Groq AI.
- Historial de resumenes por fecha.
- Interfaz limpia en el panel de Filament.

---

## Comandos utiles

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Regenerar cache de configuracion (produccion)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
php artisan migrate

# Crear usuario admin desde consola
php artisan make:filament-user
```

---

## Notas de seguridad para produccion

1. **NUNCA** subas el archivo `.env` a git. Ya esta en `.gitignore`.
2. Cambia `APP_DEBUG=false` en produccion.
3. Usa HTTPS (`APP_URL=https://...`).
4. Cambia las contrasenas por defecto de la base de datos.
5. Genera una nueva `APP_KEY` en cada instalacion.
6. Cambia el `SESSION_DOMAIN` a tu dominio real.
7. Regenera las contrasenas de aplicacion de Gmail si cambias de cuenta.

---

*Documentacion generada para el proyecto de estadia UTJ 2026.*
