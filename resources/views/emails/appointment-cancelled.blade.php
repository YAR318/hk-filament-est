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
            background: linear-gradient(135deg, #ef4444, #dc2626);
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
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 16px;
            border-radius: 8px;
            margin: 16px 0;
        }

        .info-card p {
            margin: 6px 0;
            font-size: 14px;
            color: #374151;
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
            <h1> Cita Cancelada</h1>
        </div>
        <div class="body">
            <p>La siguiente cita ha sido cancelada:</p>

            <div class="info-card">
                <p><strong> Cliente:</strong> {{ $appointment->client_name }}</p>
                <p><strong> Teléfono:</strong> {{ $appointment->client_phone }}</p>
                <p><strong> Fecha:</strong> {{ $appointment->scheduled_at->format('d/m/Y') }}</p>
                <p><strong> Hora:</strong> {{ $appointment->scheduled_at->format('H:i') }}</p>
            </div>

            <p style="color: #6b7280; font-size: 13px;">El evento de Google Calendar y Meet asociado han sido
                eliminados.</p>
        </div>
        <div class="footer">
            <p>HunabKu - Sistema de Gestión de Citas</p>
        </div>
    </div>
</body>

</html>