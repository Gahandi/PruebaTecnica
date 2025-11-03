<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RolespacesPermission
 * 
 * @property int $id
 * @property int $role_space_id
 * @property int $permission_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Permission $permission
 * @property Rolespace $role_space
 *
 * @package App\Models
 */
class RolespacesPermission extends Model
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
		return $this->belongsTo(Rolespace::class);
	}
}
