<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\User;
use App\Models\RoleSpace;
use App\Models\RoleSpacePermission;
use App\Models\SpacesUser;
use Illuminate\Http\Request;

class SpaceManagementController extends Controller
{
    /**
     * Update user role in space
     */
    public function updateUserRole(Request $request, $subdomain, User $user)
    {
        $space = Space::where('subdomain', $subdomain)->firstOrFail();

        // Verify current user is admin of space
        if (!auth()->user()->isAdminOfSpace($space->id)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para modificar roles'], 403);
        }

        $request->validate([
            'role_space_id' => 'required|exists:role_spaces,id'
        ]);

        // Find the spaces_users record
        $spaceUser = SpacesUser::where('space_id', $space->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if (!$spaceUser) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado en el espacio'], 404);
        }

        $spaceUser->update(['role_space_id' => $request->role_space_id]);

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado correctamente'
        ]);
    }

    /**
     * Remove user from space
     */
    public function removeUser(Request $request, $subdomain, User $user)
    {
        $space = Space::where('subdomain', $subdomain)->firstOrFail();

        // Verify current user is admin of space
        if (!auth()->user()->isAdminOfSpace($space->id)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para eliminar usuarios'], 403);
        }

        // Don't allow removing yourself
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'No puedes eliminarte a ti mismo'], 400);
        }

        // Soft delete the spaces_users record
        $spaceUser = SpacesUser::where('space_id', $space->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if (!$spaceUser) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado en el espacio'], 404);
        }

        $spaceUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado del espacio'
        ]);
    }

    /**
     * Invite user to space
     */
    public function inviteUser(Request $request, $subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->firstOrFail();

        // Verify current user is admin of space
        if (!auth()->user()->isAdminOfSpace($space->id)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para invitar usuarios'], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'role_space_id' => 'required|exists:role_spaces,id'
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No existe un usuario con ese email. El usuario debe registrarse primero.'
            ], 404);
        }

        // Check if user is already in space
        $existingMembership = SpacesUser::where('space_id', $space->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->exists();

        if ($existingMembership) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario ya es miembro de este espacio'
            ], 400);
        }

        // Check if there's a soft-deleted membership (re-invite)
        $deletedMembership = SpacesUser::where('space_id', $space->id)
            ->where('user_id', $user->id)
            ->whereNotNull('deleted_at')
            ->first();

        if ($deletedMembership) {
            $deletedMembership->restore();
            $deletedMembership->update(['role_space_id' => $request->role_space_id]);
        } else {
            SpacesUser::create([
                'space_id' => $space->id,
                'user_id' => $user->id,
                'role_space_id' => $request->role_space_id
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario agregado al espacio exitosamente'
        ]);
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request, $subdomain, RoleSpace $role)
    {
        $space = Space::where('subdomain', $subdomain)->firstOrFail();

        // Verify current user is admin of space
        if (!auth()->user()->isAdminOfSpace($space->id)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para modificar permisos'], 403);
        }

        // Don't allow modifying admin role permissions
        if ($role->name === 'admin') {
            return response()->json(['success' => false, 'message' => 'No se pueden modificar los permisos del rol admin'], 400);
        }

        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
            'enabled' => 'required|boolean'
        ]);

        $permissionId = $request->permission_id;
        $enabled = $request->enabled;

        if ($enabled) {
            // Add permission if not exists
            $exists = RoleSpacePermission::where('role_space_id', $role->id)
                ->where('permission_id', $permissionId)
                ->whereNull('deleted_at')
                ->exists();

            if (!$exists) {
                // Check for soft-deleted record
                $deleted = RoleSpacePermission::where('role_space_id', $role->id)
                    ->where('permission_id', $permissionId)
                    ->whereNotNull('deleted_at')
                    ->first();

                if ($deleted) {
                    $deleted->restore();
                } else {
                    RoleSpacePermission::create([
                        'role_space_id' => $role->id,
                        'permission_id' => $permissionId
                    ]);
                }
            }
        } else {
            // Remove permission
            RoleSpacePermission::where('role_space_id', $role->id)
                ->where('permission_id', $permissionId)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => $enabled ? 'Permiso habilitado' : 'Permiso deshabilitado'
        ]);
    }

    /**
     * Follow a space (become a follower/viewer)
     */
    public function followSpace(Request $request, $subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->firstOrFail();
        $user = auth()->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Debes iniciar sesión'], 401);
        }

        // Check if already a member
        $existingMembership = SpacesUser::where('space_id', $space->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->exists();

        if ($existingMembership) {
            return response()->json([
                'success' => false,
                'message' => 'Ya eres miembro de este espacio'
            ], 400);
        }

        // Check for soft-deleted record
        $deletedMembership = SpacesUser::where('space_id', $space->id)
            ->where('user_id', $user->id)
            ->whereNotNull('deleted_at')
            ->first();

        if ($deletedMembership) {
            $deletedMembership->restore();
            $deletedMembership->update(['role_space_id' => 3]); // 3 = viewer (follower)
        } else {
            SpacesUser::create([
                'space_id' => $space->id,
                'user_id' => $user->id,
                'role_space_id' => 3 // 3 = viewer (follower)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ahora sigues a ' . $space->name
        ]);
    }

    /**
     * Unfollow a space
     */
    public function unfollowSpace(Request $request, $subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->firstOrFail();
        $user = auth()->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Debes iniciar sesión'], 401);
        }

        // Find the membership with viewer role
        $spaceUser = SpacesUser::where('space_id', $space->id)
            ->where('user_id', $user->id)
            ->where('role_space_id', 3) // Only allow unfollowing if they're a follower (viewer)
            ->whereNull('deleted_at')
            ->first();

        if (!$spaceUser) {
            return response()->json([
                'success' => false,
                'message' => 'No sigues este espacio o tienes un rol diferente'
            ], 404);
        }

        $spaceUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'Has dejado de seguir a ' . $space->name
        ]);
    }

    /**
     * Unfollow a space by subdomain (for main domain requests)
     */
    public function unfollowSpaceBySubdomain(Request $request, $subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->firstOrFail();
        $user = auth()->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Debes iniciar sesión'], 401);
        }

        // Find the membership with viewer role
        $spaceUser = SpacesUser::where('space_id', $space->id)
            ->where('user_id', $user->id)
            ->where('role_space_id', 3) // Only allow unfollowing if they're a follower (viewer)
            ->whereNull('deleted_at')
            ->first();

        if (!$spaceUser) {
            return response()->json([
                'success' => false,
                'message' => 'No sigues este espacio o tienes un rol diferente'
            ], 404);
        }

        $spaceUser->delete();

        return response()->json([
            'success' => true,
            'message' => 'Has dejado de seguir a ' . $space->name
        ]);
    }
}
