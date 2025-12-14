{{-- Skeleton Loader Component --}}
@props(['type' => 'text', 'lines' => 3])

@if($type === 'text')
    <div class="animate-pulse space-y-3">
        @for($i = 0; $i < $lines; $i++)
            <div class="h-4 bg-gray-200 rounded {{ $i === $lines - 1 ? 'w-3/4' : 'w-full' }}"></div>
        @endfor
    </div>
@elseif($type === 'card')
    <div class="animate-pulse">
        <div class="bg-gray-200 h-48 rounded-t-lg"></div>
        <div class="p-4 space-y-3">
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-full"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
        </div>
    </div>
@elseif($type === 'table')
    <div class="animate-pulse space-y-3">
        @for($i = 0; $i < 5; $i++)
            <div class="flex space-x-4">
                <div class="h-12 bg-gray-200 rounded flex-1"></div>
                <div class="h-12 bg-gray-200 rounded flex-1"></div>
                <div class="h-12 bg-gray-200 rounded flex-1"></div>
            </div>
        @endfor
    </div>
@elseif($type === 'circle')
    <div class="animate-pulse">
        <div class="w-16 h-16 bg-gray-200 rounded-full"></div>
    </div>
@endif