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
 * Class TypeEvent
 * 
 * @property int $id
 * @property string $name
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Event[] $events
 *
 * @package App\Models
 */
class TypeEvent extends Model
{
	use SoftDeletes;
	protected $table = 'type_events';

	protected $fillable = [
		'name',
		'image'
	];

	public function events()
	{
		return $this->hasMany(Event::class, 'type_events_id');
	}
}
