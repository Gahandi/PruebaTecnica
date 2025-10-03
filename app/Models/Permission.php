<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Permission
 * 
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|ModelHasPermission[] $model_has_permissions
 * @property Collection|RoleCollaborator[] $role_collaborators
 * @property Collection|Role[] $roles
 *
 * @package App\Models
 */
class Permission extends Model
{
	use SoftDeletes;
	protected $table = 'permissions';

	protected $fillable = [
		'name',
		'guard_name',
		'description'
	];

	public function model_has_permissions()
	{
		return $this->hasMany(ModelHasPermission::class);
	}

	public function role_collaborators()
	{
		return $this->belongsToMany(RoleCollaborator::class, 'role_collaborators_permissions')
					->withPivot('id', 'deleted_at')
					->withTimestamps();
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'role_has_permissions');
	}
}
