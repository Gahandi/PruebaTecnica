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
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $phone
 * @property string $image
 * @property Carbon|null $email_verified_at
 * @property bool|null $verified
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Checkin[] $checkins
 * @property Collection|Collaborator[] $collaborators
 * @property Collection|Order[] $orders
 * @property Collection|UsersCode[] $users_codes
 *
 * @package App\Models
 */
class User extends Model
{
	use SoftDeletes;
	protected $table = 'users';

	protected $casts = [
		'email_verified_at' => 'datetime',
		'verified' => 'bool'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'last_name',
		'phone',
		'image',
		'email_verified_at',
		'verified',
		'email',
		'password',
		'role',
		'remember_token'
	];

	public function checkins()
	{
		return $this->hasMany(Checkin::class, 'scanned_by');
	}

	public function collaborators()
	{
		return $this->belongsToMany(Collaborator::class, 'collaborators_users')
					->withPivot('id', 'role_collaborator_id', 'deleted_at')
					->withTimestamps();
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}

	public function users_codes()
	{
		return $this->hasMany(UsersCode::class);
	}
}
