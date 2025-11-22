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
 * Class Payment
 * 
 * @property int $id
 * @property string $order_id
 * @property int $coupon_id
 * @property float $subtotal
 * @property float $discount_amount
 * @property string $status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Coupon $coupon
 * @property Order $order
 * @property Collection|PaymentsPartial[] $payments_partials
 *
 * @package App\Models
 */
class Payment extends Model
{
	use SoftDeletes;
	protected $table = 'payments';

	protected $casts = [
		'coupon_id' => 'int',
		'subtotal' => 'float',
		'discount_amount' => 'float',
		'total' => 'float',
		'taxes' => 'float'
	];

	protected $fillable = [
		'order_id',
		'coupon_id',
		'status',
		'subtotal',
		'discount_amount',
		'total',
		'taxes'
	];

	public function coupon()
	{
		return $this->belongsTo(Coupon::class);
	}

	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function payments_partials()
	{
		return $this->hasMany(PaymentsPartial::class);
	}
}
