<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TicketsEvent
 * 
 * @property int $id
 * @property int $event_id
 * @property int $ticket_types_id
 * @property int $quantity
 * @property float $price
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Event $event
 * @property TicketType $ticket_type
 *
 * @package App\Models
 */
class TicketsEvent extends Model
{
	use SoftDeletes;
	protected $table = 'tickets_events';

	protected $casts = [
		'event_id' => 'int',
		'ticket_types_id' => 'int',
		'quantity' => 'int',
		'price' => 'decimal:2'
	];

	protected $fillable = [
		'event_id',
		'ticket_types_id',
		'quantity',
		'price'
	];

	public function event()
	{
		return $this->belongsTo(Event::class);
	}

	public function ticket_type()
	{
		return $this->belongsTo(TicketType::class, 'ticket_types_id');
	}
}
