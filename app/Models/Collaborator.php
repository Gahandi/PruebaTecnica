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
 * Class Collaborator
 * 
 * @property int $id
 * @property string $name
 * @property string $openpay_id
 * @property string $reference
 * @property string $description
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|User[] $users
 * @property Collection|Event[] $events
 *
 * @package App\Models
 */
class Collaborator extends Model
{
	use SoftDeletes;
	protected $table = 'collaborators';

	protected $fillable = [
		'name',
		'openpay_id',
		'reference',
		'description'
	];

	public function users()
	{
		return $this->belongsToMany(User::class, 'collaborators_users')
					->withPivot('id', 'role_collaborator_id', 'deleted_at')
					->withTimestamps();
	}

	public function events()
	{
		return $this->hasMany(Event::class, 'collaborators_id');
	}
}
