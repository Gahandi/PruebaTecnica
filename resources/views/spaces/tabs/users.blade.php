<!-- Tab: Usuarios -->
<div>
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Usuarios del Espacio</h2>
    
    @if(count($usersWithStats) > 0)
        <div class="grid grid-cols-1 gap-6">
            @foreach($usersWithStats as $userStat)
                @php
                    $user = $userStat['user'];
                    $ticketsOwned = $userStat['tickets_owned'];
                    $revenue = $userStat['revenue'];
                    $ordersCount = $userStat['orders_count'];
                    $role = $userStat['role'];
                    $isAdmin = $userStat['is_admin'];
                @endphp
                <div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <!-- Información del Usuario -->
                        <div class="flex items-center space-x-4 flex-1">
                            <!-- Avatar -->
                            <div class="relative">
                                @if($user->image)
                                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($user->image) }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-16 h-16 rounded-full object-cover border-4 border-gray-200">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xl font-bold border-4 border-gray-200">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                @if($isAdmin)
                                    <div class="absolute -bottom-1 -right-1 bg-yellow-400 rounded-full p-1 border-2 border-white">
                                        <svg class="w-4 h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Datos del Usuario -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-1">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $user->name }} {{ $user->last_name }}</h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $isAdmin ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $role }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            </div>
                        </div>

                        <!-- Estadísticas del Usuario -->
                        <div class="grid grid-cols-3 gap-6">
                            <!-- Boletos -->
                            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                                <div class="text-3xl font-bold text-blue-600 mb-1">{{ $ticketsOwned }}</div>
                                <div class="text-xs font-medium text-blue-800">Boletos</div>
                            </div>
                            
                            <!-- Órdenes -->
                            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                                <div class="text-3xl font-bold text-green-600 mb-1">{{ $ordersCount }}</div>
                                <div class="text-xs font-medium text-green-800">Órdenes</div>
                            </div>
                            
                            <!-- Ingresos -->
                            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                                <div class="text-3xl font-bold text-purple-600 mb-1">${{ number_format($revenue, 0) }}</div>
                                <div class="text-xs font-medium text-purple-800">Ingresos</div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles Adicionales -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                                <span><strong>{{ $ticketsOwned }}</strong> boletos adquiridos</span>
                            </div>
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span><strong>{{ $ordersCount }}</strong> órdenes realizadas</span>
                            </div>
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><strong>${{ number_format($revenue, 2) }}</strong> en ingresos</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl shadow-xl">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">No hay usuarios aún</h3>
            <p class="text-lg text-gray-600">Este espacio aún no tiene miembros registrados.</p>
        </div>
    @endif
</div>

