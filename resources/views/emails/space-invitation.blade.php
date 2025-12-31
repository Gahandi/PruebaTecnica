<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(to right, #ec4899, #db2777);
            padding: 30px 20px;
            text-align: center;
        }

        .logo {
            height: 48px;
            width: auto;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 8px;
        }

        .content {
            padding: 40px 30px;
            text-align: center;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 20px;
        }

        .text {
            font-size: 16px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .space-name {
            color: #db2777;
            font-weight: bold;
            font-size: 18px;
        }

        .button {
            display: inline-block;
            background-color: #db2777;
            color: #ffffff;
            padding: 16px 32px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 50px;
            box-shadow: 0 4px 6px rgba(219, 39, 119, 0.25);
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #be185d;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }

        .link {
            color: #db2777;
            word-break: break-all;
        }
    </style>
</head>

<body>
    @php
        // Construct URL using the main app URL to avoid subdomains
        $baseUrl = rtrim(config('app.url'), '/');
        // Ensure we don't double slash if app.url has one, though rtrim handles it
        // Check if app.url already has the scheme
        if (!preg_match("~^(?:f|ht)tps?://~i", $baseUrl)) {
            $baseUrl = "http://" . $baseUrl;
        }

        $actionUrl = $baseUrl . '/invitations/' . $invitation->token;

        // Logo URL
        $logoUrl = $baseUrl . '/images/logo/Logo_merrycolor.png';
    @endphp

    <div class="container">
        <div class="header">
            <img src="{{ $logoUrl }}" alt="MerryColor Logo" class="logo">
        </div>

        <div class="content">
            <h1 class="title">¡Has sido invitado!</h1>

            <p class="text">
                Hola,<br>
                Te han invitado a formar parte del equipo del espacio <span
                    class="space-name">{{ $space->name }}</span>.
            </p>

            <p class="text">
                Únete para colaborar y gestionar eventos juntos. Solo necesitas aceptar la invitación haciendo clic
                abajo.
            </p>

            <a href="{{ $actionUrl }}" class="button">Aceptar Invitación</a>

            <p class="text" style="font-size: 14px; margin-top: 40px; color: #6b7280;">
                Este enlace expirará el {{ \Carbon\Carbon::parse($invitation->expires_at)->format('d/m/Y') }}.
            </p>
        </div>

        <div class="footer">
            <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
            <p><a href="{{ $actionUrl }}" class="link">{{ $actionUrl }}</a></p>
            <p style="margin-top: 20px;">© {{ date('Y') }} Boletería. Todos los derechos reservados.</p>
        </div>
    </div>
</body>

</html>