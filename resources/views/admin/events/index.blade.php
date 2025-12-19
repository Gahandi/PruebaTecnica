@extends('admin.layouts.app')

@section('title', 'Eventos')

@section('content')
<div class="container mx-auto px-6 py-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Lista de Eventos
        </h1>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                            Evento
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                            Espacio
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                            Fecha del evento
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                            Creado
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50">

                            {{-- Evento --}}
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $event->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $event->active ? 'Activo' : 'Inactivo' }}
                                </div>
                            </td>

                            {{-- Espacio --}}
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $event->space?->name ?? '—' }}
                            </td>

                            {{-- Fecha evento --}}
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $event->date->format('d/m/Y H:i') }}
                            </td>

                            {{-- Creado --}}
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $event->created_at->format('d/m/Y H:i') }}
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.events.destroy', $event) }}"
                                      method="POST"
                                      onsubmit="return confirmDelete('{{ $event->name }}')"
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800"
                                            title="Eliminar evento">
                                        {{-- Icono eliminar --}}
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m2 0V5a2 2 0 012-2h2a2 2 0 012 2v2" />
                                        </svg>
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                No hay eventos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t bg-gray-50">
            {{ $events->links() }}
        </div>
    </div>
</div>

<script>
    function confirmDelete(name) {
        return confirm(
            `⚠️ ¿Estás seguro de eliminar el evento "${name}"?\n\nEsta acción eliminará el evento del sistema.`
        );
    }
</script>
@endsection
