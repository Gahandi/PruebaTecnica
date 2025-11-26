<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class spacesUser
 * 
 * @property int $id
 * @property int $user_id
 * @property int $role_space_id
 * @property int $space_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property space $space
 * @property RoleSpace $role_space
 * @property User $user
 *
 * @package App\Models
 */
class spacesUser extends Model
{
	use SoftDeletes;
	protected $table = 'spaces_users';

	protected $casts = [
		'user_id' => 'int',
		'role_space_id' => 'int',
		'space_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'role_space_id',
		'space_id'
	];

	public function space()
	{
		return $this->belongsTo(space::class);
	}

	public function role_space()
	{
		return $this->belongsTo(RoleSpace::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
