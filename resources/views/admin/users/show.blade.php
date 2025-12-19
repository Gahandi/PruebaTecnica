@extends('layouts.admin')

@section('title', 'Detalles de Usuario')

@section('admin-content')
<div class="p-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a usuarios
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" 
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- User Info Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-pink-500 to-purple-600 h-32"></div>
                    <div class="px-6 pb-6">
                        <div class="-mt-16 mb-4">
                            @if($user->image)
                                <img class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover mx-auto" 
                                     src="{{ \App\Helpers\ImageHelper::getImageUrl($user->image) }}" alt="{{ $user->name }}">
                            @else
                                <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center text-white text-4xl font-bold mx-auto">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="text-center">
                            <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }} {{ $user->last_name }}</h2>
                            <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                            @if($user->phone)
                                <p class="text-gray-500 text-sm mt-1">{{ $user->phone }}</p>
                            @endif
                        </div>
                        
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Rol:</span>
                                @if($user->role === 'admin')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">👑 Admin</span>
                                @elseif($user->role === 'staff')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">🛠️ Staff</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">👤 Usuario</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Estado:</span>
                                @if($user->verified_at)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">✓ Verificado</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">⏳ Pendiente</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Registro:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats & Activity --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Stats Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-blue-500">
                        <p class="text-sm text-gray-600">Órdenes</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_orders']) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-green-500">
                        <p class="text-sm text-gray-600">Completadas</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['completed_orders']) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-purple-500">
                        <p class="text-sm text-gray-600">Total Gastado</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($stats['total_spent'], 2) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-pink-500">
                        <p class="text-sm text-gray-600">Tickets</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_tickets']) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-indigo-500">
                        <p class="text-sm text-gray-600">Espacios</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['spaces_count']) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-yellow-500">
                        <p class="text-sm text-gray-600">Check-ins</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['checkins_made']) }}</p>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Actividad Reciente</h3>
                    </div>
                    <div class="p-6">
                        @if($recentActivity->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentActivity as $activity)
                                    <div class="flex items-start space-x-3 pb-4 border-b border-gray-100 last:border-0">
                                        <div class="flex-shrink-0">
                                            <span class="text-2xl">{{ $activity->icon }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No hay actividad reciente</p>
                        @endif
                    </div>
                </div>

                {{-- Spaces --}}
                @if($user->spaces->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900">Espacios</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($user->spaces as $space)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition-colors">
                                        <h4 class="font-semibold text-gray-900">{{ $space->name }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $space->subdomain }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
</div>
@endsection
