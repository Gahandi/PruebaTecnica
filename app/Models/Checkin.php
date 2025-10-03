<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Checkin
 * 
 * @property int $id
 * @property string $ticket_id
 * @property Carbon $scanned_at
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $scanned_by
 * 
 * @property User|null $user
 * @property Ticket $ticket
 *
 * @package App\Models
 */
class Checkin extends Model
{
	use SoftDeletes;
	protected $table = 'checkins';

	protected $casts = [
		'scanned_at' => 'datetime',
		'scanned_by' => 'int'
	];

	protected $fillable = [
		'ticket_id',
		'scanned_at',
		'scanned_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'scanned_by');
	}

	public function ticket()
	{
		return $this->belongsTo(Ticket::class);
	}
}
