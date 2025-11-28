<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Space extends Model
{
    use SoftDeletes;
    
    protected $table = 'spaces';

    protected $fillable = [
        'name',
        'openpay_id',
        'reference',
        'subdomain',
        'description',
        'banner',
        'logo',
        'color_primary',
        'color_secondary',
        'about',
        'location',
        'website',
        'contact_email',
        'contact_phone',
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'keywords'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'spaces_users')
                    ->withPivot('id', 'role_space_id', 'deleted_at')
                    ->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'spaces_id');
    }
}