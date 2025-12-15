<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Reporte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #e91e63;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #e91e63;
            margin: 0;
            font-size: 28px;
        }

        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stats-row {
            display: table-row;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }

        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .stat-growth {
            font-size: 10px;
            margin-top: 5px;
        }

        .growth-positive {
            color: #10b981;
        }

        .growth-negative {
            color: #ef4444;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #e91e63;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e91e63;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #e91e63;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .metric-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .metric-cell {
            display: table-cell;
            width: 50%;
            padding: 10px;
        }

        .metric-label {
            font-size: 11px;
            color: #666;
        }

        .metric-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Dashboard - Reporte General</h1>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Métricas Principales --}}
    <div class="stats-grid">
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-label">Total Eventos</div>
                <div class="stat-value">{{ number_format($totalEvents) }}</div>
                <div class="stat-growth">{{ number_format($activeEvents) }} activos</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Órdenes Completadas</div>
                <div class="stat-value">{{ number_format($completedOrders) }}</div>
                <div class="stat-growth">de {{ number_format($totalOrders) }} totales</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Ingresos Totales</div>
                <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-growth {{ $revenueGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                    {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}% vs período anterior
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Usuarios</div>
                <div class="stat-value">{{ number_format($totalUsers) }}</div>
                <div class="stat-growth">{{ number_format($userStats['verified']) }} verificados</div>
            </div>
        </div>
    </div>

    {{-- Métricas Adicionales --}}
    <div class="metric-row">
        <div class="metric-cell">
            <div class="metric-label">Tasa de Conversión</div>
            <div class="metric-value">{{ number_format($conversionRate, 1) }}%</div>
        </div>
        <div class="metric-cell">
            <div class="metric-label">Valor Promedio de Orden</div>
            <div class="metric-value">${{ number_format($averageOrderValue, 2) }}</div>
        </div>
    </div>

    <div class="metric-row">
        <div class="metric-cell">
            <div class="metric-label">Total Tickets</div>
            <div class="metric-value">{{ number_format($totalTickets) }}</div>
        </div>
        <div class="metric-cell">
            <div class="metric-label">Tasa de Check-in</div>
            <div class="metric-value">{{ number_format($checkinRate, 1) }}%</div>
        </div>
    </div>

    {{-- Eventos Más Populares --}}
    @if($popularEvents->count() > 0)
        <div class="section">
            <div class="section-title">Top 10 Eventos Más Populares</div>
            <table>
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Fecha</th>
                        <th>Órdenes</th>
                        <th>Ingresos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($popularEvents as $event)
                        <tr>
                            <td>{{ $event->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}</td>
                            <td>{{ number_format($event->orders_count) }}</td>
                            <td>${{ number_format($event->revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Top Compradores --}}
    @if($topBuyers->count() > 0)
        <div class="section">
            <div class="section-title">Top 10 Compradores</div>
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Órdenes</th>
                        <th>Total Gastado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topBuyers as $buyer)
                        <tr>
                            <td>{{ $buyer->name }}</td>
                            <td>{{ $buyer->email }}</td>
                            <td>{{ number_format($buyer->orders_count) }}</td>
                            <td>${{ number_format($buyer->total_spent, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Estadísticas de Usuarios --}}
    <div class="section">
        <div class="section-title">Distribución de Usuarios</div>
        <div class="metric-row">
            <div class="metric-cell">
                <div class="metric-label">Administradores</div>
                <div class="metric-value">{{ number_format($userStats['admins']) }}</div>
            </div>
            <div class="metric-cell">
                <div class="metric-label">Staff</div>
                <div class="metric-value">{{ number_format($userStats['staff']) }}</div>
            </div>
        </div>
        <div class="metric-row">
            <div class="metric-cell">
                <div class="metric-label">Usuarios Regulares</div>
                <div class="metric-value">{{ number_format($userStats['regular']) }}</div>
            </div>
            <div class="metric-cell">
                <div class="metric-label">Verificados</div>
                <div class="metric-value">{{ number_format($userStats['verified']) }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema de gestión de eventos</p>
        <p>© {{ now()->year }} - Todos los derechos reservados</p>
    </div>
</body>

</html>