<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu cuenta</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #1e293b; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background-color: #0f172a; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: 700; color: #ffffff;">hunabku</h1>
                            <p style="margin: 8px 0 0; font-size: 14px; color: #94a3b8;">Plataforma de Gestión Interna</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 20px 40px;">
                            <p style="margin: 0 0 16px; font-size: 16px; color: #e2e8f0;">
                                ¡Hola, <strong>{{ $name }}</strong>! 👋
                            </p>
                            <p style="margin: 0 0 24px; font-size: 14px; color: #94a3b8; line-height: 1.6;">
                                Gracias por registrarte en Hunabku. Para verificar tu cuenta, ingresa el siguiente código en la pantalla de verificación:
                            </p>

                            <!-- Token Code -->
                            <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; padding: 24px; text-align: center; margin: 0 0 24px;">
                                <p style="margin: 0 0 8px; font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px;">Tu código de verificación</p>
                                <p style="margin: 0; font-size: 36px; font-weight: 700; color: #ef4444; letter-spacing: 8px; font-family: monospace;">{{ $token }}</p>
                            </div>

                            <p style="margin: 0 0 8px; font-size: 13px; color: #94a3b8; line-height: 1.6;">
                                ⏰ Este código expira en <strong style="color: #e2e8f0;">30 minutos</strong>.
                            </p>
                            <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.6;">
                                Si no creaste esta cuenta, puedes ignorar este correo.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 40px 40px; border-top: 1px solid rgba(255,255,255,0.05);">
                            <p style="margin: 0; font-size: 12px; color: #475569; text-align: center;">
                                &copy; {{ date('Y') }} Hunabku. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
