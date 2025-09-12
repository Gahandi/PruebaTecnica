<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleto - {{ $ticket->ticketType->event->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
        }
        .ticket {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .ticket-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-item {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .info-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #111827;
            font-weight: 500;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
        }
        .qr-code img {
            max-width: 180px;
            max-height: 180px;
        }
        .status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.valid {
            background: #dcfce7;
            color: #166534;
        }
        .status.used {
            background: #fef2f2;
            color: #dc2626;
        }
        .footer {
            background: #f3f4f6;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .ticket-id {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
        }
        .instructions {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
        }
        .instructions h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #92400e;
        }
        .instructions ul {
            margin: 0;
            padding-left: 20px;
            font-size: 12px;
            color: #92400e;
        }
        .instructions li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Header -->
        <div class="header">
            <h1>{{ $ticket->ticketType->event->name }}</h1>
            <p>{{ \Carbon\Carbon::parse($ticket->ticketType->event->date)->format('l, d F Y \a \l\a\s H:i') }}</p>
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
                
                <div class="info-item">
                    <div class="info-label">Precio</div>
                    <div class="info-value">${{ number_format($ticket->ticketType->price, 2) }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Ubicación</div>
                    <div class="info-value">{{ $ticket->ticketType->event->location }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Estado</div>
                    <div class="info-value">
                        <span class="status {{ $ticket->checkin ? 'used' : 'valid' }}">
                            {{ $ticket->checkin ? 'Canjeado' : 'Válido' }}
                        </span>
                    </div>
                </div>
                
                @if($ticket->checkin)
                <div class="info-item">
                    <div class="info-label">Canjeado el</div>
                    <div class="info-value">{{ $ticket->checkin->scanned_at->format('d/m/Y H:i') }}</div>
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
                <div class="qr-code">
                    @if($ticket->qr_url && file_exists(public_path($ticket->qr_url)))
                        @if(pathinfo($ticket->qr_url, PATHINFO_EXTENSION) === 'svg')
                            @php
                                $svgContent = file_get_contents(public_path($ticket->qr_url));
                                $base64Svg = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                            @endphp
                            <img src="{{ $base64Svg }}" alt="QR Code" style="width: 180px; height: 180px; display: block; margin: 0 auto;">
                        @elseif(pathinfo($ticket->qr_url, PATHINFO_EXTENSION) === 'txt')
                            <div style="color: #6b7280; font-size: 14px; text-align: center;">
                                <div style="font-size: 24px; margin-bottom: 8px;">📱</div>
                                <div>QR Code</div>
                            </div>
                        @else
                            <img src="{{ public_path($ticket->qr_url) }}" alt="QR Code" style="width: 180px; height: 180px; display: block; margin: 0 auto;">
                        @endif
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
            <p><strong>{{ config('app.name', 'Laravel') }}</strong></p>
            <p>Este es un boleto electrónico válido. No se requiere impresión física.</p>
            <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
