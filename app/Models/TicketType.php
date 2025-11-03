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
 * Class TicketType
 * 
 * @property int $id
 * @property string $name
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Ticket[] $tickets
 * @property Collection|TicketsEvent[] $tickets_events
 *
 * @package App\Models
 */
class TicketType extends Model
{
	use SoftDeletes;
	protected $table = 'ticket_types';

	protected $fillable = [
		'name'
	];

	public function tickets()
	{
		return $this->hasMany(Ticket::class, 'ticket_types_id');
	}

	public function tickets_events()
	{
		return $this->hasMany(TicketsEvent::class, 'ticket_types_id');
	}

	public function events()
	{
		return $this->belongsToMany(Event::class, 'tickets_events', 'ticket_types_id', 'event_id')
			->withPivot('quantity', 'price')
			->withTimestamps();
	}
}
