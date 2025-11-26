<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Class RoleSpacePermission
 * 
 * @property int $id
 * @property int $role_space_id
 * @property int $permission_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Permission $permission
 * @property RoleSpace $role_space
 *
 * @package App\Models
 */
class RoleSpacePermission extends Model
{
	use SoftDeletes;
	protected $table = 'role_spaces_permissions';

	protected $casts = [
		'role_space_id' => 'int',
		'permission_id' => 'int'
	];

	protected $fillable = [
		'role_space_id',
		'permission_id'
	];

	public function permission()
	{
		return $this->belongsTo(Permission::class);
	}

	public function role_space()
	{
		return $this->belongsTo(RoleSpace::class);
	}

	/**
	 * Obtiene los permisos del usuario autenticado para un espacio específico
	 * 
	 * @param int $spaceId El ID del espacio
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public static function getPermissionsForAuthenticatedUser($spaceId)
	{
		// Verificar que el usuario esté autenticado
		if (!Auth::check()) {
			return collect([]);
		}

		$user = Auth::user();

		// Obtener el role_space_id del usuario en el espacio dado
		$spaceUser = $user->spaces()
			->where('spaces.id', $spaceId)
			->wherePivotNull('deleted_at')
			->first();

		if (!$spaceUser) {
			return collect([]);
		}

		$roleSpaceId = $spaceUser->pivot->role_space_id;

		// Obtener los permisos asociados al role_space_id
		$permissions = self::where('role_space_id', $roleSpaceId)
			->whereNull('deleted_at')
			->with('permission')
			->get()
			->pluck('permission')
			->filter();

		return $permissions;
	}

	/**
	 * Verifica si el usuario autenticado tiene un permiso específico en un espacio
	 * 
	 * @param int $spaceId El ID del espacio
	 * @param string $permissionName El nombre del permiso a verificar
	 * @return bool
	 */
	public static function hasPermission($spaceId, $permissionName)
	{
		$permissions = self::getPermissionsForAuthenticatedUser($spaceId);
		
		return $permissions->contains(function ($permission) use ($permissionName) {
			return $permission && $permission->name === $permissionName;
		});
	}
}