<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceInvitation extends Model
{
    protected $fillable = [
        'space_id',
        'email',
        'role_space_id',
        'token',
        'expires_at',
    ];

    /**
     * Get the space that owns the invitation.
     */
    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * Get the role for the invitation.
     */
    public function role()
    {
        return $this->belongsTo(RoleSpace::class, 'role_space_id');
    }
}
