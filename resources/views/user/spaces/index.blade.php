@extends('layouts.app')

@section('title', 'Mis Espacios')

@section('content')
    @php
        $currentUser = auth()->user();

        // Separate spaces by role using pivot data (no extra queries)
        // The pivot is already loaded via the spaces() relationship
        $mySpaces = $spaces->filter(fn($space) => $space->pivot->role_space_id == 1); // Admin
        $staffSpaces = $spaces->filter(fn($space) => $space->pivot->role_space_id == 2); // Staff
        $followingSpaces = $spaces->filter(fn($space) => $space->pivot->role_space_id == 3); // Follower

        $isAdminOfAny = $mySpaces->count() > 0;
    @endphp

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#e24972]">Mis Espacios</h1>
                    <p class="text-gray-600 mt-1">Gestiona tus espacios y los que sigues</p>
                </div>
                <div class="flex space-x-3">
                    @if(!$isAdminOfAny)
                        <a href="{{ route('user.spaces.create') }}"
                            class="bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Crear Mi Cajón
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="mb-10">
            @if ($errors->any())
                <p class="text-sm text-red-600">{{ $errors->first() }}</p>
            @endif
        </div>
        <!-- My Spaces (Admin) -->
        @if($mySpaces->count() > 0)
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                        </path>
                    </svg>
                    Mi Espacio
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($mySpaces as $space)
                        @include('user.spaces._space_card', ['space' => $space, 'roleLabel' => 'Administrador'])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Staff Spaces -->
        @if($staffSpaces->count() > 0)
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                        <path
                            d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z">
                        </path>
                    </svg>
                    Espacios Donde Soy Staff
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($staffSpaces as $space)
                        @include('user.spaces._space_card', ['space' => $space, 'roleLabel' => 'Staff'])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Following Spaces -->
        @if($followingSpaces->count() > 0)
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    Espacios que Sigo
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($followingSpaces as $space)
                        @include('user.spaces._space_card', ['space' => $space, 'roleLabel' => 'Siguiendo'])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Empty State -->
        @if($spaces->count() == 0)
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No tienes espacios aún</h3>
                <p class="text-gray-600 mb-8">Crea tu cajón de eventos o sigue espacios de otros organizadores.</p>
                <div class="flex justify-center">
                    <a href="{{ route('user.spaces.create') }}"
                        class="bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white px-6 py-3 rounded-lg transition-colors">
                        Crear Mi Cajón
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function () {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                notification.textContent = 'URL copiada al portapapeles';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            });
        }
    </script>
@endsection