<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles; 

class User extends Authenticatable
{
	use SoftDeletes, HasRoles; 

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
		'role'
	];

	public function checkins()
	{
		return $this->hasMany(Checkin::class, 'scanned_by');
	}

	public function spaces()
	{
		return $this->belongsToMany(Space::class, 'spaces_users')
					->withPivot('id', 'role_space_id', 'deleted_at')
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

    /**
     * Check if user is admin of a specific space
     */
    public function isAdminOfSpace($spaceId)
    {
        return $this->spaces()
            ->where('spaces.id', $spaceId)
            ->wherePivot('role_space_id', 1) // 1 = admin role
            ->wherePivotNull('deleted_at') // Excluir relaciones eliminadas
            ->exists();
    }

    /**
     * Check if user has any role in a specific space
     */
    public function hasRoleInSpace($spaceId, $roleName = null)
    {
        $query = $this->spaces()
            ->where('spaces.id', $spaceId);
        
        if ($roleName) {
            $query->whereHas('role_space', function($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }
        
        return $query->exists();
    }
}
