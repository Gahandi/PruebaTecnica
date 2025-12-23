{{-- Space Card Partial --}}
<div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100 hover:shadow-2xl transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-900">{{ $space->name }}</h3>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
            @if($roleLabel == 'Administrador') bg-pink-100 text-pink-800
            @elseif($roleLabel == 'Staff') bg-blue-100 text-blue-800
            @else bg-purple-100 text-purple-800
            @endif">
            {{ $roleLabel }}
        </span>
    </div>

    <p class="text-gray-600 mb-4 line-clamp-3">{{ $space->description }}</p>

    <div class="mb-4">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                </path>
            </svg>
            {{ $space->events->count() }} eventos
        </div>
        <div class="flex items-center text-sm text-gray-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
            </svg>
            {{ $space->subdomain }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}
        </div>
    </div>

    <div class="flex space-x-2">
        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}" target="_blank"
            class="flex-1 bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white text-center py-2 px-4 rounded-lg transition-colors">
            Ver Espacio
        </a>
        <button onclick="copyToClipboard('{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}')"
            class="bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                </path>
            </svg>
        </button>
    </div>
</div>