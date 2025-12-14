{{-- Tooltip Component --}}
@props(['text', 'position' => 'top'])

@php
    $positionClasses = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
    ];
@endphp

<div class="relative inline-block group">
    {{ $slot }}
    <div
        class="absolute {{ $positionClasses[$position] }} invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-50">
        <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 whitespace-nowrap shadow-lg">
            {{ $text }}
            <div class="absolute w-2 h-2 bg-gray-900 transform rotate-45 
                @if($position === 'top') -bottom-1 left-1/2 -translate-x-1/2
                @elseif($position === 'bottom') -top-1 left-1/2 -translate-x-1/2
                @elseif($position === 'left') -right-1 top-1/2 -translate-y-1/2
                @else -left-1 top-1/2 -translate-y-1/2 @endif">
            </div>
        </div>
    </div>
</div>