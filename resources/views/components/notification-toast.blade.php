{{-- Notification Toast Component --}}
@props(['type' => 'success', 'message', 'duration' => 3000])

@php
    $typeClasses = [
        'success' => 'bg-green-500',
        'error' => 'bg-red-500',
        'warning' => 'bg-yellow-500',
        'info' => 'bg-blue-500',
    ];

    $icons = [
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
        'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    ];
@endphp

<div id="toast-{{ $type }}"
    class="fixed bottom-4 right-4 {{ $typeClasses[$type] }} text-white px-6 py-4 rounded-lg shadow-2xl transform transition-all duration-300 translate-y-0 opacity-100 flex items-center space-x-3 max-w-md z-50"
    style="display: none;">
    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $icons[$type] !!}
    </svg>
    <p class="flex-1">{{ $message }}</p>
    <button onclick="this.parentElement.style.display='none'" class="text-white hover:text-gray-200 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>

<script>
    function showToast(type, message, duration = {{ $duration }}) {
        const toast = document.getElementById(`toast-${type}`);
        if (toast) {
            toast.querySelector('p').textContent = message;
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(100%)';
                setTimeout(() => {
                    toast.style.display = 'none';
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                }, 300);
            }, duration);
        }
    }
</script>