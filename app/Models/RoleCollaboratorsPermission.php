<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RoleCollaboratorsPermission
 * 
 * @property int $id
 * @property int $role_collaborator_id
 * @property int $permission_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Permission $permission
 * @property RoleCollaborator $role_collaborator
 *
 * @package App\Models
 */
class RoleCollaboratorsPermission extends Model
{
	use SoftDeletes;
	protected $table = 'role_collaborators_permissions';

	protected $casts = [
		'role_collaborator_id' => 'int',
		'permission_id' => 'int'
	];

	protected $fillable = [
		'role_collaborator_id',
		'permission_id'
	];

	public function permission()
	{
		return $this->belongsTo(Permission::class);
	}

	public function role_collaborator()
	{
		return $this->belongsTo(RoleCollaborator::class);
	}
}
