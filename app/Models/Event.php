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
 * Class Event
 * 
 * @property int $id
 * @property string $name
 * @property Carbon $date
 * @property string $address
 * @property string $coordinates
 * @property bool $active
 * @property string $description
 * @property int $type_events_id
 * @property int $spaces_id
 * @property int $state_id
 * @property string $image
 * @property string $banner
 * @property string $banner_app
 * @property string $icon
 * @property string $agenda
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property space $space
 * @property State $state
 * @property TypeEvent $type_event
 * @property Collection|Order[] $orders
 * @property Collection|TicketsEvent[] $tickets_events
 *
 * @package App\Models
 */
class Event extends Model
{
	use SoftDeletes;
	protected $table = 'events';

	protected $casts = [
		'date' => 'datetime',
		'active' => 'bool',
		'type_events_id' => 'int',
		'spaces_id' => 'int',
		'state_id' => 'int'
	];

	protected $fillable = [
		'name',
		'date',
		'address',
		'coordinates',
		'slug',
		'active',
		'description',
		'type_events_id',
		'spaces_id',
		'state_id',
		'image',
		'banner',
		'banner_app',
		'icon',
		'agenda'
	];

	public function space()
	{
		return $this->belongsTo(space::class, 'spaces_id');
	}

	public function state()
	{
		return $this->belongsTo(State::class);
	}

	public function type_event()
	{
		return $this->belongsTo(TypeEvent::class, 'type_events_id');
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}

	public function tickets_events()
	{
		return $this->hasMany(TicketsEvent::class);
	}

	public function ticketTypes()
	{
		return $this->belongsToMany(TicketType::class, 'tickets_events', 'event_id', 'ticket_types_id')
			->withPivot('quantity', 'price')
			->withTimestamps();
	}
}
