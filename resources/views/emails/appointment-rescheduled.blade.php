<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            padding: 24px;
            text-align: center;
        }

        .header h1 {
            color: #fff;
            margin: 0;
            font-size: 22px;
        }

        .body {
            padding: 28px;
        }

        .info-card {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            border-radius: 8px;
            margin: 16px 0;
        }

        .info-card p {
            margin: 6px 0;
            font-size: 14px;
            color: #374151;
        }

        .old-date {
            text-decoration: line-through;
            color: #9ca3af;
        }

        .new-date {
            color: #059669;
            font-weight: 700;
        }

        .meet-btn {
            display: inline-block;
            background: #4285F4;
            color: #fff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            margin: 12px 0;
        }

        .footer {
            text-align: center;
            padding: 16px;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1> Cita Reagendada</h1>
        </div>
        <div class="body">
            <p>La siguiente cita ha sido reagendada:</p>

            <div class="info-card">
                <p><strong> Cliente:</strong> {{ $appointment->client_name }}</p>
                <p><strong> Teléfono:</strong> {{ $appointment->client_phone }}</p>
                <p><strong> Fecha anterior:</strong> <span class="old-date">{{ $oldDateTime->format('d/m/Y H:i')
                        }}</span></p>
                <p><strong> Nueva fecha:</strong> <span class="new-date">{{ $appointment->scheduled_at->format('d/m/Y
                        H:i') }}</span></p>
                <p><strong>⏱ Duración:</strong> {{ $appointment->duration_minutes }} minutos</p>
            </div>

            @if($appointment->meet_link)
            <p style="text-align: center;">
                <a href="{{ $appointment->meet_link }}" class="meet-btn"> Unirse a Google Meet</a>
            </p>
            @endif
        </div>
        <div class="footer">
            <p>HunabKu - Sistema de Gestión de Citas</p>
        </div>
    </div>
</body>

</html>