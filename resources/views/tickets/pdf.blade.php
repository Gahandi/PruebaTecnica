<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleto - {{ $ticket->event->name }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', sans-serif;
            background: #fdf2f8;
            color: #111827;
        }

        .ticket {
            max-width: 550px;
            margin: 20px auto;
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .logo-wrapper {
            text-align: center;
            width: 100%;
        }

        .logo {
            display: block;
            margin: 15px auto 0 auto;
            width: 140px;
        }

        /* HEADER */
        .header {
            padding: 15px 5px;
            text-align: center;
            color: black;
        }

        .header h1 {
            margin: 0;
            font-size: 30px;
            letter-spacing: 1px;
            color: #f8279fff;
        }

        .header p {
            margin-top: 8px;
            font-size: 14px;
            opacity: 0.95;
        }

        /* PERFORATED LINE */
        .perforation {
            border-top: 2px dashed #e5e7eb;
            margin: 0;
        }

        /* CONTENT */
        .content {
            padding: 10px;
        }

        .ticket-id {
            text-align: center;
            font-size: 18px;
            letter-spacing: 2px;
            font-weight: bold;
            color: #f8279fff;
            margin-bottom: 20px;
        }

        /* INFO GRID */
        .ticket-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .info-label {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 15px;
            font-weight: bold;
        }

        /* STATUS */
        .status {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            display: inline-block;
        }

        .status.valid {
            background: #dcfce7;
            color: #166534;
        }

        .status.used {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* QR */
        .qr-section {
            margin-top: 20px;
            padding: 20px;
            border-radius: 14px;
            background: #f9fafb;
            text-align: center;
        }

        .qr-section h3 {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .qr-code img {
            width: 200px;
            height: 200px;
            border-radius: 12px;
            border: 3px solid #f472b6;
        }

        /* FOOTER */
        .footer {
            background: #f8279fff;
            color: white;
            text-align: center;
            padding: 18px;
            font-size: 11px;
        }
    </style>

</head>
<body>
    <div class="ticket">
        
        <div class="logo-wrapper">
            <img src="{{ public_path('images/logo/Logo_merrycolor.png') }}"
                alt="Logo Merrycolor"
                class="logo">
        </div>

        <!-- Header -->
        <div class="header">
            <h1>{{ $ticket->event->name }}</h1>
            <p>{{ \Carbon\Carbon::parse($ticket->event->date)->format('l, d F Y \a \l\a\s H:i') }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Ticket ID -->
            <div class="ticket-id">
                Boleto #{{ substr($ticket->id, 0, 8) }}
            </div>

            <!-- Ticket Information -->
            <div class="ticket-info">
                <div class="info-item">
                    <div class="info-label">Tipo de Boleto</div>
                    <div class="info-value">{{ $ticket->ticketType->name }}</div>
                </div>
                
                <!-- <div class="info-item">
                    <div class="info-label">Precio</div>
                    <div class="info-value">${{ number_format($ticket->ticketType->price, 2) }}</div>
                </div> -->
                
                <div class="info-item">
                    <div class="info-label">Ubicación</div>
                    <div class="info-value">{{ $ticket->event->address }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Estado</div>
                    <div class="info-value">
                        <span class="status {{ $ticket->used ? 'used' : 'valid' }}">
                            {{ $ticket->used ? 'Canjeado' : 'Válido' }}
                        </span>
                    </div>
                </div>
                
                @if($ticket->used)
                <div class="info-item">
                    <div class="info-label">Canjeado el</div>
                    <div class="info-value">{{ $ticket->updated_at->format('d/m/Y H:i') }}</div>
                </div>
                @endif
                
                <div class="info-item">
                    <div class="info-label">Comprador</div>
                    <div class="info-value">{{ auth()->user()->name }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Correo</div>
                    <div class="info-value">{{ auth()->user()->email }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Fecha de Compra</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="qr-section">
                <h3 style="margin: 0 0 15px 0; color: #374151;">Código QR de Entrada</h3>
                <div class="" style="text-align: center; margin-top: 20px;">
                    @if($ticket->qr_base64)
                        <img src="{{ $ticket->qr_base64 }}" 
                            alt="QR Code" 
                            style="width: 180px; height: 180px; margin: 0 auto;">
                    @else
                        <div style="color: #9ca3af; font-size: 12px;">
                            QR no disponible
                        </div>
                    @endif
                </div>
                <p style="margin: 0; font-size: 12px; color: #6b7280;">
                    Presenta este código QR en la entrada del evento
                </p>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>Instrucciones Importantes</h3>
                <ul>
                    <li>Presenta este boleto en la entrada del evento</li>
                    <li>El código QR será escaneado para validar tu entrada</li>
                    <li>Llega con anticipación al evento</li>
                    <li>Conserva este boleto hasta el final del evento</li>
                    <li>En caso de problemas, contacta al organizador</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Este es un boleto electrónico válido. No se requiere impresión física.</strong></p>
            <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
