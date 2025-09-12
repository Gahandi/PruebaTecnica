@extends('layouts.app')

@section('title', 'Sistema de Check-in - Staff')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sistema de Check-in - Staff</h1>
            <p class="text-gray-600">Escanea códigos QR para validar boletos</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- QR Scanner -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Escanear Código QR</h2>
            </div>
            <div class="p-6">
                <!-- Manual QR Input -->
                <div class="mb-6">
                    <label for="qr_input" class="block text-sm font-medium text-gray-700 mb-2">
                        Código QR del Boleto
                    </label>
                    <div class="flex space-x-2">
                        <input type="text" 
                               id="qr_input" 
                               placeholder="Escanea o ingresa el código QR manualmente"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button onclick="scanQR()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Validar
                        </button>
                    </div>
                </div>

                <!-- Camera Scanner (Placeholder) -->
                <div class="bg-gray-100 rounded-lg p-8 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <p class="text-gray-500">Cámara de escaneo QR</p>
                    <p class="text-sm text-gray-400 mt-2">Usa el campo de arriba para ingresar códigos manualmente</p>
                </div>

                <!-- Result -->
                <div id="scan_result" class="mt-6 hidden">
                    <div class="p-4 rounded-md">
                        <div id="result_content"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Check-ins -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-900">Check-ins Recientes</h2>
                    <div class="flex space-x-2">
                        <!-- Filter by Event -->
                        <form method="GET" action="{{ route('staff.checkins.index') }}" class="flex items-center space-x-2">
                            <select name="event_id" onchange="this.form.submit()" class="px-3 py-1 border border-gray-300 rounded-md text-sm">
                                <option value="">Todos los eventos</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @if($checkins->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($checkins as $checkin)
                            <div class="p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h3 class="text-sm font-medium text-gray-900">
                                                {{ $checkin->ticket->order->user->name }}
                                            </h3>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Verificado
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $checkin->ticket->ticketType->event->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $checkin->ticket->ticketType->name }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            Ticket #{{ $checkin->ticket->id }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-900">
                                            {{ $checkin->scanned_at->format('H:i') }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $checkin->scanned_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">No hay check-ins registrados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Check-ins Hoy</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $checkins->where('scanned_at', '>=', today())->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Check-ins</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $checkins->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Último Check-in</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        @if($checkins->count() > 0)
                            {{ $checkins->first()->scanned_at->format('H:i') }}
                        @else
                            --
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function scanQR() {
    const qrCode = document.getElementById('qr_input').value.trim();
    
    if (!qrCode) {
        showResult('Por favor ingresa un código QR', 'error');
        return;
    }

    // Mostrar loading
    showResult('Validando boleto...', 'loading');

    // Enviar petición AJAX
    fetch('{{ route("staff.checkins.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            qr_code: qrCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResult(`
                <div class="text-green-800">
                    <h3 class="font-semibold">✓ Check-in Exitoso</h3>
                    <p class="text-sm mt-1">Cliente: ${data.ticket_info.customer}</p>
                    <p class="text-sm">Evento: ${data.ticket_info.event}</p>
                    <p class="text-sm">Tipo: ${data.ticket_info.ticket_type}</p>
                    <p class="text-sm">Hora: ${data.ticket_info.checked_in_at}</p>
                </div>
            `, 'success');
            
            // Limpiar input
            document.getElementById('qr_input').value = '';
            
            // Recargar página después de 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showResult(`
                <div class="text-red-800">
                    <h3 class="font-semibold">✗ ${data.message}</h3>
                    ${data.ticket_info ? `
                        <p class="text-sm mt-1">Cliente: ${data.ticket_info.customer}</p>
                        <p class="text-sm">Evento: ${data.ticket_info.event}</p>
                        <p class="text-sm">Tipo: ${data.ticket_info.ticket_type}</p>
                        <p class="text-sm">Ya usado: ${data.checkin_at}</p>
                    ` : ''}
                </div>
            `, 'error');
        }
    })
    .catch(error => {
        showResult('Error al validar el boleto. Inténtalo de nuevo.', 'error');
    });
}

function showResult(message, type) {
    const resultDiv = document.getElementById('scan_result');
    const contentDiv = document.getElementById('result_content');
    
    resultDiv.classList.remove('hidden');
    contentDiv.innerHTML = message;
    
    // Remover clases anteriores
    resultDiv.classList.remove('bg-green-50', 'bg-red-50', 'bg-yellow-50', 'border-green-200', 'border-red-200', 'border-yellow-200');
    
    // Agregar clases según el tipo
    if (type === 'success') {
        resultDiv.classList.add('bg-green-50', 'border-green-200');
    } else if (type === 'error') {
        resultDiv.classList.add('bg-red-50', 'border-red-200');
    } else if (type === 'loading') {
        resultDiv.classList.add('bg-yellow-50', 'border-yellow-200');
    }
    
    resultDiv.classList.add('border', 'rounded-md');
}

// Permitir Enter para escanear
document.getElementById('qr_input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        scanQR();
    }
});
</script>
@endsection
