<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\SoftDeletes;



/**
 * Class Ticket
 *
 * @property string $id
 * @property string $order_id
 * @property int $ticket_types_id
 * @property bool $used
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Order $order
 * @property TicketType $ticket_type
 * @property Collection|Checkin[] $checkins
 *
 * @package App\Models
 */
class Ticket extends Model
{
	use SoftDeletes;
	protected $table = 'tickets';
	public $incrementing = false;

	protected $casts = [
		'ticket_types_id' => 'int',
		'event_id' => 'int',
		'used' => 'bool'
	];

	protected $fillable = [
		'order_id',
		'ticket_types_id',
		'event_id',
		'used'
	];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function ticketType()
	{
		return $this->belongsTo(TicketType::class, 'ticket_types_id');
	}

    public function eventTicket()
	{
		return $this->belongsTo(Event::class, 'event_id');
	}

	public function checkin()
	{
		return $this->hasMany(Checkin::class);
	}

	/**
	 * Obtener el precio del boleto desde la relación con el evento
	 */
	public function getPrice()
	{
		$ticketEvent = \App\Models\TicketsEvent::where('ticket_types_id', $this->ticket_types_id)
			->where('event_id', $this->order->event_id)
			->first();

		return $ticketEvent ? $ticketEvent->price : 0;
	}
}
