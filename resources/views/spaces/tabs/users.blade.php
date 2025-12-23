<!-- Tab: Usuarios - Enhanced Version with Management -->
<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Gestión de Usuarios</h2>
        
        @if($isAdmin)
        <button type="button" onclick="openInviteModal()" 
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-lg shadow-lg hover:from-pink-600 hover:to-pink-700 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Invitar Usuario
        </button>
        @endif
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @php
            $adminCount = collect($usersWithStats)->where('is_admin', true)->count();
            $staffCount = collect($usersWithStats)->where('role', 'staff')->count();
            $viewerCount = collect($usersWithStats)->count() - $adminCount - $staffCount;
        @endphp
        <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-xs sm:text-sm font-medium">Administradores</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ $adminCount }}</p>
                </div>
                <svg class="w-8 h-8 text-yellow-200" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs sm:text-sm font-medium">Staff</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ $staffCount }}</p>
                </div>
                <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-xs sm:text-sm font-medium">Visualizadores</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ $viewerCount }}</p>
                </div>
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    @if(count($usersWithStats) > 0)
        <div class="grid grid-cols-1 gap-4 sm:gap-6">
            @foreach($usersWithStats as $userStat)
                @php
                    $user = $userStat['user'];
                    $ticketsOwned = $userStat['tickets_owned'];
                    $revenue = $userStat['revenue'];
                    $ordersCount = $userStat['orders_count'];
                    $role = $userStat['role'];
                    $userIsAdmin = $userStat['is_admin'];
                    
                    // Obtener role_space_id actual
                    $userSpace = $user->spaces()->where('spaces.id', $space->id)->first();
                    $currentRoleId = $userSpace ? $userSpace->pivot->role_space_id : null;
                @endphp
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 hover:shadow-2xl transition-all duration-300 border border-gray-100" id="user-card-{{ $user->id }}">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <!-- Información del Usuario -->
                        <div class="flex items-center space-x-3 sm:space-x-4 flex-1">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                @if($user->image)
                                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($user->image) }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-12 h-12 sm:w-16 sm:h-16 rounded-full object-cover border-4 border-gray-200">
                                @else
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-lg sm:text-xl font-bold border-4 border-gray-200">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                @if($userIsAdmin)
                                    <div class="absolute -bottom-1 -right-1 bg-yellow-400 rounded-full p-1 border-2 border-white">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Datos del Usuario -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-base sm:text-xl font-bold text-gray-900 truncate">{{ $user->name }} {{ $user->last_name }}</h3>
                                    @if($isAdmin)
                                        @php
                                            $isCurrentUser = $user->id === auth()->id();
                                        @endphp
                                        @if($isCurrentUser)
                                            {{-- Current user - show badge without selector --}}
                                            <span class="ml-2 text-xs font-semibold rounded-full px-3 py-1 bg-yellow-100 text-yellow-800 border-2 border-yellow-300 cursor-not-allowed opacity-75" title="No puedes cambiar tu propio rol">
                                                {{ ucfirst($roleSpaces->firstWhere('id', $currentRoleId)?->name ?? 'Admin') }} (tú)
                                            </span>
                                        @else
                                            {{-- Other users - show selector --}}
                                            <select onchange="updateUserRole({{ $user->id }}, this.value)" 
                                                    class="ml-2 text-xs font-semibold rounded-full px-3 py-1 border-2 cursor-pointer transition-colors
                                                           {{ $currentRoleId == 1 ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : ($currentRoleId == 2 ? 'bg-blue-100 text-blue-800 border-blue-300' : 'bg-gray-100 text-gray-800 border-gray-300') }}"
                                                    id="role-select-{{ $user->id }}">
                                                @foreach($roleSpaces ?? [] as $roleSpace)
                                                    <option value="{{ $roleSpace->id }}" {{ $currentRoleId == $roleSpace->id ? 'selected' : '' }}>
                                                        {{ ucfirst($roleSpace->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $userIsAdmin ? 'bg-yellow-100 text-yellow-800' : ($role == 'staff' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs sm:text-sm text-gray-600 truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        <!-- Estadísticas del Usuario -->
                        <div class="grid grid-cols-3 gap-2 sm:gap-4 lg:gap-6">
                            <!-- Boletos -->
                            <div class="text-center p-2 sm:p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg sm:rounded-xl">
                                <div class="text-xl sm:text-3xl font-bold text-blue-600 mb-1">{{ $ticketsOwned }}</div>
                                <div class="text-xs font-medium text-blue-800">Boletos</div>
                            </div>
                            
                            <!-- Órdenes -->
                            <div class="text-center p-2 sm:p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg sm:rounded-xl">
                                <div class="text-xl sm:text-3xl font-bold text-green-600 mb-1">{{ $ordersCount }}</div>
                                <div class="text-xs font-medium text-green-800">Órdenes</div>
                            </div>
                            
                            <!-- Ingresos -->
                            <div class="text-center p-2 sm:p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg sm:rounded-xl">
                                <div class="text-lg sm:text-3xl font-bold text-purple-600 mb-1">${{ number_format($revenue, 0) }}</div>
                                <div class="text-xs font-medium text-purple-800">Ingresos</div>
                            </div>
                        </div>

                        <!-- Actions (only for admin) -->
                        @if($isAdmin && !$userIsAdmin)
                        <div class="flex items-center justify-end lg:justify-center gap-2">
                            <button onclick="confirmRemoveUser({{ $user->id }}, '{{ $user->name }}')" 
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Eliminar del espacio">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl shadow-xl">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">No hay usuarios aún</h3>
            <p class="text-lg text-gray-600 mb-6">Este espacio aún no tiene miembros registrados.</p>
            @if($isAdmin)
            <button type="button" onclick="openInviteModal()" 
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-lg shadow-lg hover:from-pink-600 hover:to-pink-700 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Invitar al primer usuario
            </button>
            @endif
        </div>
    @endif
</div>

<!-- Invite User Modal -->
<div id="inviteUserModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Invitar Usuario</h3>
                <button onclick="closeInviteModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="inviteUserForm" onsubmit="inviteUser(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email del Usuario</label>
                        <input type="email" name="email" id="inviteEmail" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                               placeholder="usuario@ejemplo.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                        <select name="role_space_id" id="inviteRole" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            @foreach($roleSpaces ?? [] as $roleSpace)
                                <option value="{{ $roleSpace->id }}">{{ ucfirst($roleSpace->name) }} - {{ $roleSpace->description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closeInviteModal()"
                            class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" id="inviteSubmitBtn"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-lg hover:from-pink-600 hover:to-pink-700 transition-all">
                        Invitar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// User Management Functions
function openInviteModal() {
    document.getElementById('inviteUserModal').classList.remove('hidden');
    document.getElementById('inviteEmail').focus();
}

function closeInviteModal() {
    document.getElementById('inviteUserModal').classList.add('hidden');
    document.getElementById('inviteUserForm').reset();
}

function inviteUser(event) {
    event.preventDefault();
    
    const email = document.getElementById('inviteEmail').value;
    const roleId = document.getElementById('inviteRole').value;
    const submitBtn = document.getElementById('inviteSubmitBtn');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Invitando...';
    
    fetch('/manage/users/invite', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            email: email,
            role_space_id: roleId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeInviteModal();
            // Show success message
            Swal.fire({
                icon: 'success',
                title: '¡Usuario invitado!',
                text: data.message || 'El usuario ha sido agregado al espacio.',
                confirmButtonColor: '#ec4899'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo invitar al usuario.',
                confirmButtonColor: '#ec4899'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar la solicitud.',
            confirmButtonColor: '#ec4899'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Invitar';
    });
}

function updateUserRole(userId, newRoleId) {
    fetch(`/manage/users/${userId}/role`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ role_space_id: newRoleId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update select styling
            const select = document.getElementById(`role-select-${userId}`);
            select.className = select.className.replace(/bg-\w+-100 text-\w+-800 border-\w+-300/g, '');
            
            if (newRoleId == 1) {
                select.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
            } else if (newRoleId == 2) {
                select.classList.add('bg-blue-100', 'text-blue-800', 'border-blue-300');
            } else {
                select.classList.add('bg-gray-100', 'text-gray-800', 'border-gray-300');
            }
            
            // Toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-pulse';
            toast.textContent = 'Rol actualizado correctamente';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo actualizar el rol.',
                confirmButtonColor: '#ec4899'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al actualizar el rol.',
            confirmButtonColor: '#ec4899'
        });
    });
}

function confirmRemoveUser(userId, userName) {
    Swal.fire({
        title: '¿Eliminar usuario?',
        html: `¿Estás seguro de eliminar a <strong>${userName}</strong> del espacio?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            removeUser(userId);
        }
    });
}

function removeUser(userId) {
    fetch(`/manage/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animate and remove the user card
            const card = document.getElementById(`user-card-${userId}`);
            if (card) {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'translateX(-100%)';
                setTimeout(() => card.remove(), 300);
            }
            
            Swal.fire({
                icon: 'success',
                title: 'Usuario eliminado',
                text: 'El usuario ha sido eliminado del espacio.',
                confirmButtonColor: '#ec4899'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo eliminar al usuario.',
                confirmButtonColor: '#ec4899'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al eliminar al usuario.',
            confirmButtonColor: '#ec4899'
        });
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeInviteModal();
    }
});

// Close modal on backdrop click
document.getElementById('inviteUserModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeInviteModal();
    }
});
</script>
