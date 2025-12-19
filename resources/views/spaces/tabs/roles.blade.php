<!-- Tab: Roles y Permisos -->
<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Roles y Permisos</h2>
            <p class="text-sm text-gray-600 mt-1">Gestiona los permisos de cada rol en tu espacio</p>
        </div>
    </div>

    <!-- Roles Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        @foreach($roleSpaces ?? [] as $roleSpace)
            @php
                $rolePermissions = $roleSpace->permissions ?? collect();
                $rolePermissionIds = $rolePermissions->pluck('id')->toArray();
                
                $roleColors = [
                    'admin' => ['bg' => 'from-yellow-400 to-yellow-500', 'text' => 'text-yellow-800', 'border' => 'border-yellow-500'],
                    'staff' => ['bg' => 'from-blue-500 to-blue-600', 'text' => 'text-blue-800', 'border' => 'border-blue-500'],
                    'viewer' => ['bg' => 'from-gray-500 to-gray-600', 'text' => 'text-gray-800', 'border' => 'border-gray-500']
                ];
                $colors = $roleColors[$roleSpace->name] ?? $roleColors['viewer'];
            @endphp
            
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 {{ $colors['border'] }}">
                <!-- Role Header -->
                <div class="bg-gradient-to-r {{ $colors['bg'] }} p-4 sm:p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold capitalize">{{ $roleSpace->name }}</h3>
                            <p class="text-sm opacity-90 mt-1">{{ $roleSpace->description }}</p>
                        </div>
                        @if($roleSpace->name === 'admin')
                            <svg class="w-8 h-8 opacity-75" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @elseif($roleSpace->name === 'staff')
                            <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        @else
                            <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        @endif
                    </div>
                </div>

                <!-- Permissions List -->
                <div class="p-4 sm:p-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Permisos</h4>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @php
                            $permissionGroups = [
                                'Eventos' => ['view events', 'create events', 'edit events', 'delete events'],
                                'Tickets' => ['view ticket types', 'create ticket types', 'edit ticket types', 'delete ticket types'],
                                'Cupones' => ['view coupons', 'create coupons', 'edit coupons', 'delete coupons'],
                                'Órdenes' => ['view orders', 'create orders', 'edit orders', 'delete orders'],
                                'Check-ins' => ['view checkins', 'create checkins', 'edit checkins', 'delete checkins'],
                                'Panel' => ['view dashboard', 'view admin panel']
                            ];
                        @endphp
                        
                        @foreach($permissionGroups as $groupName => $groupPermissions)
                            <div class="mb-4">
                                <p class="text-xs font-semibold text-gray-500 mb-2">{{ $groupName }}</p>
                                <div class="space-y-2">
                                    @foreach($allPermissions->whereIn('name', $groupPermissions) as $permission)
                                        @php
                                            $hasPermission = in_array($permission->id, $rolePermissionIds);
                                            $isAdminRole = $roleSpace->name === 'admin';
                                        @endphp
                                        <label class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors {{ $isAdminRole ? 'cursor-not-allowed opacity-75' : 'cursor-pointer' }}">
                                            <span class="text-sm text-gray-700">{{ $permission->description }}</span>
                                            <div class="relative">
                                                <input type="checkbox" 
                                                       class="sr-only permission-toggle"
                                                       data-role-id="{{ $roleSpace->id }}"
                                                       data-permission-id="{{ $permission->id }}"
                                                       {{ $hasPermission ? 'checked' : '' }}
                                                       {{ $isAdminRole ? 'disabled' : '' }}
                                                       onchange="togglePermission({{ $roleSpace->id }}, {{ $permission->id }}, this.checked)">
                                                <div class="w-10 h-6 {{ $hasPermission ? 'bg-green-500' : 'bg-gray-300' }} rounded-full shadow-inner transition-colors duration-200"></div>
                                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-200 {{ $hasPermission ? 'translate-x-4' : '' }}"></div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Permission Matrix Quick Reference -->
    <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6 overflow-x-auto">
        <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4">Matriz de Permisos</h3>
        <table class="min-w-full">
            <thead>
                <tr class="border-b-2 border-gray-200">
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Permiso</th>
                    @foreach($roleSpaces ?? [] as $roleSpace)
                        <th class="py-3 px-4 text-center text-sm font-semibold text-gray-700 capitalize">{{ $roleSpace->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($allPermissions ?? [] as $permission)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $permission->description }}</td>
                        @foreach($roleSpaces ?? [] as $roleSpace)
                            @php
                                $rolePermissions = $roleSpace->permissions ?? collect();
                                $hasPermission = $rolePermissions->contains('id', $permission->id);
                            @endphp
                            <td class="py-3 px-4 text-center">
                                @if($hasPermission)
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 text-red-400 rounded-full">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function togglePermission(roleId, permissionId, isEnabled) {
    fetch(`/manage/roles/${roleId}/permissions`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            permission_id: permissionId,
            enabled: isEnabled
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update toggle visual state
            const input = document.querySelector(`input[data-role-id="${roleId}"][data-permission-id="${permissionId}"]`);
            if (input) {
                const container = input.parentElement;
                const track = container.querySelector('div:first-of-type');
                const thumb = container.querySelector('div:last-of-type');
                
                if (isEnabled) {
                    track.classList.remove('bg-gray-300');
                    track.classList.add('bg-green-500');
                    thumb.classList.add('translate-x-4');
                } else {
                    track.classList.remove('bg-green-500');
                    track.classList.add('bg-gray-300');
                    thumb.classList.remove('translate-x-4');
                }
            }
            
            // Toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            toast.textContent = isEnabled ? 'Permiso habilitado' : 'Permiso deshabilitado';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo actualizar el permiso.',
                confirmButtonColor: '#ec4899'
            });
            // Revert the checkbox
            const input = document.querySelector(`input[data-role-id="${roleId}"][data-permission-id="${permissionId}"]`);
            if (input) input.checked = !isEnabled;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al actualizar el permiso.',
            confirmButtonColor: '#ec4899'
        });
        // Revert the checkbox
        const input = document.querySelector(`input[data-role-id="${roleId}"][data-permission-id="${permissionId}"]`);
        if (input) input.checked = !isEnabled;
    });
}
</script>

<style>
/* Custom toggle switch styling */
input.permission-toggle:checked + div {
    background-color: #22c55e;
}
input.permission-toggle:checked + div + div {
    transform: translateX(1rem);
}
</style>
