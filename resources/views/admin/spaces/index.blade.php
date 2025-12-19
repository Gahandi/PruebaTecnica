@extends('layouts.admin')

@section('title', 'Espacios')

@section('admin-content')
<div class="p-6 max-w-7xl mx-auto">

    <h1 class="text-3xl font-bold mb-6">Espacios y usuarios</h1>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">
                            Espacio
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">
                            Usuarios asignados
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">
                            Creado
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($spaces as $space)
                        <tr class="hover:bg-gray-50 align-top">
                            {{-- Espacio --}}
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $space->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $space->subdomain }}
                                </div>
                            </td>

                            {{-- Usuarios --}}
                            <td class="px-6 py-4">
                                @if($space->users->count())
                                    <ul class="space-y-2">
                                        @foreach($space->users as $user)
                                            <li class="text-sm">
                                                <span class="font-medium text-gray-900">
                                                    {{ $user->name }} {{ $user->last_name }}
                                                </span>
                                                <span class="text-gray-500 text-xs">
                                                    ({{ $user->email }})
                                                </span>

                                                <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800">
                                                    Rol ID: {{ $user->pivot->role_space_id }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-sm text-gray-400">
                                        Sin usuarios asignados
                                    </span>
                                @endif
                            </td>

                            {{-- Fecha --}}
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $space->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                No hay espacios registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-gray-50 border-t">
            {{ $spaces->links() }}
        </div>
    </div>
</div>
@endsection
