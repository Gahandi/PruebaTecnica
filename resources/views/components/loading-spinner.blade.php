{{-- Loading Spinner Component --}}
@props(['size' => 'md', 'color' => 'pink'])

@php
    $sizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-8 h-8',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
    ];

    $colorClasses = [
        'pink' => 'border-pink-500',
        'blue' => 'border-blue-500',
        'purple' => 'border-purple-500',
        'green' => 'border-green-500',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'inline-block']) }}>
    <div
        class="{{ $sizeClasses[$size] }} {{ $colorClasses[$color] }} border-4 border-t-transparent rounded-full animate-spin">
    </div>
</div>