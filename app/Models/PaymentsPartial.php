<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentsPartial
 * 
 * @property int $id
 * @property int $payment_id
 * @property float $payment
 * @property string $reference
 * @property string $path
 * @property string $payment_type
 * @property string $description
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 *
 * @package App\Models
 */
class PaymentsPartial extends Model
{
	use SoftDeletes;
	protected $table = 'payments_partials';

	protected $casts = [
		'payment_id' => 'int',
		'payment' => 'float'
	];

	protected $fillable = [
		'payment_id',
		'payment',
		'reference',
		'path',
		'payment_type',
		'description'
	];

	public function payment()
	{
		return $this->belongsTo(Payment::class);
	}
}
