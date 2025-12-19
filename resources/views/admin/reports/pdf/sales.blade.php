<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        .period {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11px;
            color: #555;
        }

        .stats {
            width: 100%;
            margin-bottom: 20px;
        }

        .stats td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .stats .label {
            font-weight: bold;
            background: #f5f5f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #2d3748;
            color: #fff;
        }

        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        th {
            text-align: left;
        }

        td {
            vertical-align: middle;
        }

        .right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>

    <h1>Reporte de Ventas</h1>

    <div class="period">
        Periodo:
        <strong>{{ $startDate->format('d/m/Y') }}</strong>
        —
        <strong>{{ $endDate->format('d/m/Y') }}</strong>
    </div>

    {{-- Estadísticas --}}
    <table class="stats">
        <tr>
            <td class="label">Total de Ventas</td>
            <td class="label">Órdenes</td>
            <td class="label">Promedio por Orden</td>
        </tr>
        <tr>
            <td>${{ number_format($stats['total_sales'], 2) }}</td>
            <td>{{ $stats['total_orders'] }}</td>
            <td>${{ number_format($stats['average_order'], 2) }}</td>
        </tr>
    </table>

    {{-- Tabla de ventas --}}
    <table>
        <thead>
            <tr>
                <th>ID Orden</th>
                <th>Evento</th>
                <th>Usuario</th>
                <th>Email</th>
                <th class="right">Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->event?->name ?? 'N/A' }}</td>
                    <td>{{ $order->user?->name ?? 'N/A' }}</td>
                    <td>{{ $order->user?->email ?? 'N/A' }}</td>
                    <td class="right">
                        ${{ number_format($order->total ?? 0, 2) }}
                    </td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">
                        No hay ventas en este periodo
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
