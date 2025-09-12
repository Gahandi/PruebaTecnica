@extends('layouts.app')

@section('title', 'Mis Boletos')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Boletos</h1>
            <p class="text-gray-600">Gestiona y descarga tus boletos</p>
        </div>
    </div>

    @if($orders->count() > 0)
        <!-- Orders List -->
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
                    <!-- Order Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold">Orden #{{ substr($order->id, 0, 8) }}</h3>
                                <p class="text-sm opacity-90">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">${{ number_format($order->total, 2) }}</div>
                                <div class="text-sm opacity-90">{{ $order->tickets->count() }} boleto(s)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Event Info -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Evento</h4>
                                <p class="text-gray-700">{{ $order->event->name }}</p>
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($order->event->date)->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-gray-500">{{ $order->event->location }}</p>
                            </div>

                            <!-- Order Status -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Estado</h4>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $order->status === 'completed' ? 'Completada' : ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Tickets Grid -->
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-4">Tus Boletos</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($order->tickets as $ticket)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h5 class="font-medium text-gray-900">{{ $ticket->ticketType->name }}</h5>
                                                <p class="text-sm text-gray-500">${{ number_format($ticket->ticketType->price, 2) }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ticket->checkin ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $ticket->checkin ? 'Canjeado' : 'Válido' }}
                                            </span>
                                        </div>

                                        <!-- QR Code Preview -->
                                        <div class="mb-3 text-center">
                                            @if($ticket->qr_url && file_exists(public_path($ticket->qr_url)))
                                                @if(pathinfo($ticket->qr_url, PATHINFO_EXTENSION) === 'svg')
                                                    <div class="w-16 h-16 mx-auto border border-gray-200 rounded flex items-center justify-center">
                                                        {!! file_get_contents(public_path($ticket->qr_url)) !!}
                                                    </div>
                                                @elseif(pathinfo($ticket->qr_url, PATHINFO_EXTENSION) === 'txt')
                                                    <div class="w-16 h-16 mx-auto bg-gray-100 border border-gray-200 rounded flex items-center justify-center text-xs text-gray-500">
                                                        QR Code
                                                    </div>
                                                @else
                                                    <img src="{{ asset($ticket->qr_url) }}" alt="QR Code" class="w-16 h-16 mx-auto border border-gray-200 rounded object-contain">
                                                @endif
                                            @else
                                                <div class="w-16 h-16 mx-auto bg-gray-100 border border-gray-200 rounded flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex space-x-2">
                                            <a href="{{ route('tickets.show', $ticket->id) }}" 
                                               class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded-md hover:bg-blue-700 transition-colors text-xs">
                                                Ver
                                            </a>
                                            <a href="{{ route('tickets.pdf', $ticket->id) }}" 
                                               class="flex-1 bg-red-600 text-white text-center px-3 py-2 rounded-md hover:bg-red-700 transition-colors text-xs">
                                                PDF
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes órdenes</h3>
            <p class="text-gray-500 mb-4">Compra algunos boletos para ver tus órdenes aquí.</p>
            <a href="{{ route('events.public') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Ver Eventos
            </a>
        </div>
    @endif
</div>
@endsection
