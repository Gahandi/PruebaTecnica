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
 * Class State
 * 
 * @property int $id
 * @property string $name
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Event[] $events
 * @property Collection|Order[] $orders
 *
 * @package App\Models
 */
class State extends Model
{
	use SoftDeletes;
	protected $table = 'states';

	protected $fillable = [
		'name'
	];

	public function events()
	{
		return $this->hasMany(Event::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}
}
