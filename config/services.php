<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Third Party Services
     |--------------------------------------------------------------------------
     |
     | This file is for storing the credentials for third party services such
     | as Mailgun, Postmark, AWS and more. This file provides the de facto
     | location for this type of information, allowing packages to have
     | a conventional file to locate the various service credentials.
     |
     */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'evolution' => [
        'base_url' => env('EVOLUTION_BASE_URL', 'http://evolution_api:8080'),
        'api_key' => env('EVOLUTION_API_KEY', 'evolution123'),
        'instance' => env('EVOLUTION_INSTANCE', 'HunabkuBot'),
    ],

    'whatsapp' => [
        'owner_phone' => env('BOT_OWNER_PHONE', '5217531672288'),
    ],

    'google_calendar' => [
        'credentials_path' => env('GOOGLE_CALENDAR_CREDENTIALS', storage_path('app/google-calendar-credentials.json')),
        'calendar_id' => env('GOOGLE_CALENDAR_ID', 'primary'),
        'app_name' => env('GOOGLE_CALENDAR_APP_NAME', 'HunabKu Calendar'),
    ],

    /*
     |--------------------------------------------------------------------------
     | API Interna
     |--------------------------------------------------------------------------
     |
     | Clave para proteger los endpoints de la API REST interna.
     | Si está vacía, se permite acceso sin autenticación (desarrollo local).
     |
     */
    'api_key' => env('API_INTERNAL_KEY', ''),

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
    ],

];