<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class Order
 *
 * @property string $id
 * @property int $user_id
 * @property int $event_id
 * @property int $created_by
 * @property int $state_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $status
 *
 * @property User $user
 * @property Event $event
 * @property State $state
 * @property Collection|Payment[] $payments
 * @property Collection|Ticket[] $tickets
 *
 * @package App\Models
 */
class Order extends Model
{
	use SoftDeletes;
	protected $table = 'orders';
    protected $keyType = 'string';
	public $incrementing = false;

	protected $casts = [
		'user_id' => 'int',
		'event_id' => 'string',
		'created_by' => 'int',
		'state_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'event_id',
		'created_by',
		'state_id',
		'status'
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

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function state()
	{
		return $this->belongsTo(State::class);
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public function tickets()
	{
		return $this->hasMany(Ticket::class);
	}
	public function event()
	{
		return $this->belongsTo(Event::class, 'event_id');
	}
}
