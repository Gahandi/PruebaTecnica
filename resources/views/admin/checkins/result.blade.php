@extends('layouts.app')

@section('title', 'Resultado del Check-in')
@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            @if($success)
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-green-600 mb-2">¡Check-in Exitoso!</h1>
            @else
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-red-600 mb-2">Check-in No Válido</h1>
            @endif
            <p class="text-gray-600">{{ $message }}</p>
        </div>

        <!-- Ticket Information Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r {{ $success ? 'from-green-500 to-green-600' : 'from-red-500 to-red-600' }} text-white">
                <h2 class="text-xl font-bold">Información del Boleto</h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Event Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Evento</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Nombre:</span>
                                <p class="text-gray-900">{{ $ticket->ticketType->event->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Fecha:</span>
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($ticket->ticketType->event->date)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Ubicación:</span>
                                <p class="text-gray-900">{{ $ticket->ticketType->event->location }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Details -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalles del Boleto</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Tipo de Boleto:</span>
                                <p class="text-gray-900">{{ $ticket->ticketType->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Precio:</span>
                                <p class="text-gray-900">${{ number_format($ticket->ticketType->price, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Comprador:</span>
                                <p class="text-gray-900">{{ $ticket->order->user->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Email:</span>
                                <p class="text-gray-900">{{ $ticket->order->user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Check-in Information -->
                @if($success && isset($checkin))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Check-in</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Fecha y Hora:</span>
                                <p class="text-gray-900">{{ $checkin->scanned_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Escaneado por:</span>
                                <p class="text-gray-900">{{ $checkin->scannedBy->name ?? 'Sistema' }}</p>
                            </div>
                        </div>
                    </div>
                @elseif(!$success && isset($checkin_at))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Check-in Anterior</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Fecha y Hora:</span>
                                <p class="text-gray-900">{{ $checkin_at }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Estado:</span>
                                <p class="text-red-600 font-medium">Ya utilizado</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- QR Code Display -->
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Código QR</h3>
                    @if($ticket->qr_url && file_exists(public_path($ticket->qr_url)))
                        <img src="{{ asset($ticket->qr_url) }}" alt="QR Code" class="w-32 h-32 mx-auto border border-gray-200 rounded-lg object-contain">
                    @else
                        <div class="w-32 h-32 mx-auto bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex justify-center space-x-4">
            <a href="{{ route('admin.checkins.index') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors">
                Volver a Check-ins
            </a>
            <a href="{{ route('tickets.show', $ticket->id) }}" 
               class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 transition-colors">
                Ver Boleto Completo
            </a>
        </div>
    </div>
</div>
@endsection
