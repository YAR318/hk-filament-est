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
            background: linear-gradient(135deg, #10b981, #059669);
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
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 16px;
            border-radius: 8px;
            margin: 16px 0;
        }

        .info-card p {
            margin: 6px 0;
            font-size: 14px;
            color: #374151;
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
            <h1> Cita Confirmada</h1>
        </div>
        <div class="body">
            <p>Se ha programado una nueva cita con los siguientes detalles:</p>

            <div class="info-card">
                <p><strong> Cliente:</strong> {{ $appointment->client_name }}</p>
                <p><strong> Teléfono:</strong> {{ $appointment->client_phone }}</p>
                @if($appointment->client_email)
                <p><strong> Email:</strong> {{ $appointment->client_email }}</p>
                @endif
                <p><strong> Fecha:</strong> {{ $appointment->scheduled_at->format('d/m/Y') }}</p>
                <p><strong> Hora:</strong> {{ $appointment->scheduled_at->format('H:i') }}</p>
                <p><strong>⏱ Duración:</strong> {{ $appointment->duration_minutes }} minutos</p>
                @if($appointment->notes)
                <p><strong> Notas:</strong> {{ $appointment->notes }}</p>
                @endif
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