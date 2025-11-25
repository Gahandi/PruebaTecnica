<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
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
        .code-box {
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #667eea;
            font-family: 'Courier New', monospace;
            padding: 20px;
            background: #f3f4f6;
            border-radius: 6px;
            display: inline-block;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Verificación de Email</h1>
        <p>Hola, {{ $user->name }}</p>
    </div>

    <div class="content">
        <p>Gracias por registrarte. Para completar tu verificación, por favor ingresa el siguiente código:</p>

        <div class="code-box">
            <p style="margin-top: 0; color: #6b7280;">Tu código de verificación es:</p>
            <div class="code">{{ $code }}</div>
            <p style="margin-bottom: 0; color: #6b7280; font-size: 12px;">Este código expira en 24 horas</p>
        </div>

        <p>Ingresa este código en la página de verificación para activar tu cuenta.</p>

        <div class="warning">
            <strong>⚠️ Importante:</strong> Si no solicitaste este código, puedes ignorar este correo. Tu cuenta no será verificada sin el código.
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ config('app.name', 'Sistema de Boletos') }}</strong></p>
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
    </div>
</body>
</html>

