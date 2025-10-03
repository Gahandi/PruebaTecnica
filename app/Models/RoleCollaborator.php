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
 * Class RoleCollaborator
 * 
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|CollaboratorsUser[] $collaborators_users
 * @property Collection|Permission[] $permissions
 *
 * @package App\Models
 */
class RoleCollaborator extends Model
{
	use SoftDeletes;
	protected $table = 'role_collaborators';

	protected $fillable = [
		'name',
		'description'
	];

	public function collaborators_users()
	{
		return $this->hasMany(CollaboratorsUser::class);
	}

	public function permissions()
	{
		return $this->belongsToMany(Permission::class, 'role_collaborators_permissions')
					->withPivot('id', 'deleted_at')
					->withTimestamps();
	}
}
