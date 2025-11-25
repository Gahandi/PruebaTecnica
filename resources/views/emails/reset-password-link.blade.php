<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            color: #92400e;
        }
        .link-fallback {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            word-break: break-all;
            font-size: 12px;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Restablecer Contraseña</h1>
        <p>Hola, {{ $user->name }}</p>
    </div>

    <div class="content">
        <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>

        <p>Si solicitaste este cambio, haz clic en el botón a continuación para crear una nueva contraseña:</p>

        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Restablecer Contraseña</a>
        </div>

        <div class="link-fallback">
            <p style="margin: 0 0 8px 0; font-weight: bold; color: #374151;">Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
            <p style="margin: 0; word-break: break-all;">{{ $resetUrl }}</p>
        </div>

        <div class="warning">
            <strong>⚠️ Importante:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Este enlace expirará en 60 minutos</li>
                <li>Si no solicitaste este cambio, puedes ignorar este correo de forma segura</li>
                <li>Tu contraseña no cambiará hasta que hagas clic en el enlace y crees una nueva</li>
            </ul>
        </div>

        <p style="margin-top: 20px;">Si tienes problemas, puedes solicitar un nuevo enlace desde la página de inicio de sesión.</p>
    </div>

    <div class="footer">
        <p><strong>{{ config('app.name', 'Sistema de Boletos') }}</strong></p>
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
    </div>
</body>
</html>


