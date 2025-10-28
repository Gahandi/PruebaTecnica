@php
$instructions = \App\Helpers\SubdomainHelper::getDevelopmentInstructions($space->subdomain);
@endphp

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <h3 class="text-sm font-medium text-blue-800 mb-2">{{ $instructions['message'] }}</h3>
            @if(isset($instructions['steps']))
                <ul class="text-sm text-blue-700 space-y-1">
                    @foreach($instructions['steps'] as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="mt-3">
                <a href="{{ $instructions['url'] }}" 
                   target="_blank"
                   class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Abrir Cajón
                </a>
            </div>
        </div>
    </div>
</div>
