<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            height: 50px;
        }

        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .button {
            display: inline-block;
            background-color: #ec4899;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- You can add your logo here -->
            <h2>Has sido invitado a unirse a un espacio</h2>
        </div>

        <div class="content">
            <p>Hola,</p>
            <p>Has recibido una invitación para unirte al espacio <strong>{{ $space->name }}</strong> en nuestra
                plataforma.</p>

            <p>Para aceptar la invitación y unirte al equipo, haz clic en el siguiente botón:</p>

            <div style="text-align: center;">
                <a href="{{ route('invitations.accept', $invitation->token) }}" class="button">Aceptar Invitación</a>
            </div>

            <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
            <p style="font-size: 14px; color: #666; word-break: break-all;">
                {{ route('invitations.accept', $invitation->token) }}
            </p>

            <p>Esta invitación expirará el {{ \Carbon\Carbon::parse($invitation->expires_at)->format('d/m/Y') }}.</p>
        </div>

        <div class="footer">
            <p>Si no esperabas esta invitación, puedes ignorar este correo.</p>
        </div>
    </div>
</body>

</html>