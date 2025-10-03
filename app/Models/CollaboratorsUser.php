<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CollaboratorsUser
 * 
 * @property int $id
 * @property int $user_id
 * @property int $role_collaborator_id
 * @property int $collaborator_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collaborator $collaborator
 * @property RoleCollaborator $role_collaborator
 * @property User $user
 *
 * @package App\Models
 */
class CollaboratorsUser extends Model
{
	use SoftDeletes;
	protected $table = 'collaborators_users';

	protected $casts = [
		'user_id' => 'int',
		'role_collaborator_id' => 'int',
		'collaborator_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'role_collaborator_id',
		'collaborator_id'
	];

	public function collaborator()
	{
		return $this->belongsTo(Collaborator::class);
	}

	public function role_collaborator()
	{
		return $this->belongsTo(RoleCollaborator::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
