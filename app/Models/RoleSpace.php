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
 * Class RoleSpace
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|spacesUser[] $spaces_users
 * @property Collection|Permission[] $permissions
 *
 * @package App\Models
 */
class RoleSpace extends Model
{
	use SoftDeletes;
	protected $table = 'role_spaces';

	protected $fillable = [
		'name',
		'description'
	];

	public function spaces_users()
	{
		return $this->hasMany(spacesUser::class);
	}

	public function permissions()
	{
		return $this->belongsToMany(Permission::class, 'role_spaces_permissions')
					->withPivot('id', 'deleted_at')
					->withTimestamps();
	}
}
