<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tus Boletos - {{ $order->id }}</title>
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
        .order-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
        }
        .info-value {
            color: #111827;
        }
        .tickets-list {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .ticket-item {
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .ticket-item:last-child {
            margin-bottom: 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>¡Compra Exitosa!</h1>
        <p>Gracias por tu compra, {{ $customerName }}</p>
    </div>

    <div class="content">
        <div class="order-info">
            <h2 style="margin-top: 0;">Detalles de tu Orden</h2>
            <div class="info-row">
                <span class="info-label">Número de Orden:</span>
                <span class="info-value">#{{ substr($order->id, 0, 8) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Subtotal:</span>
                <span class="info-value">${{ number_format($order->subtotal, 2) }} MXN</span>
            </div>
            @if($order->discount_amount > 0)
            <div class="info-row">
                <span class="info-label">Descuento:</span>
                <span class="info-value">-${{ number_format($order->discount_amount, 2) }} MXN</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">IVA (16%):</span>
                <span class="info-value">${{ number_format($order->taxes, 2) }} MXN</span>
            </div>
            <div class="info-row" style="font-size: 18px; font-weight: bold; border-top: 2px solid #e5e7eb; padding-top: 15px; margin-top: 10px;">
                <span class="info-label">Total:</span>
                <span class="info-value">${{ number_format($order->total, 2) }} MXN</span>
            </div>
        </div>

        <div class="tickets-list">
            <h2 style="margin-top: 0;">Tus Boletos</h2>
            <p>Se han adjuntado los PDFs de tus boletos a este correo. Cada boleto incluye un código QR único para el acceso al evento.</p>
            
            @foreach($tickets as $ticket)
            <div class="ticket-item">
                <strong>{{ $ticket->eventTicket->name ?? 'Evento' }}</strong><br>
                <small>Tipo: {{ $ticket->ticketType->name ?? 'Boleto' }}</small><br>
                <small>ID: {{ substr($ticket->id, 0, 8) }}</small>
            </div>
            @endforeach
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('tickets.my') }}" class="btn">Ver Mis Boletos</a>
        </div>

        <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 15px; margin-top: 20px;">
            <h3 style="margin-top: 0; color: #92400e;">Instrucciones Importantes</h3>
            <ul style="color: #92400e; margin: 0; padding-left: 20px;">
                <li>Presenta el código QR de tu boleto en la entrada del evento</li>
                <li>Llega con anticipación al evento</li>
                <li>Conserva este correo y los PDFs hasta el final del evento</li>
                <li>En caso de problemas, contacta al organizador</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ config('app.name', 'Sistema de Boletos') }}</strong></p>
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        <p>Si tienes alguna pregunta, contacta a soporte.</p>
    </div>
</body>
</html>

