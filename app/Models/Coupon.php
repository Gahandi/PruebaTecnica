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
 * Class Coupon
 * 
 * @property int $id
 * @property string $code
 * @property int $discount_percentage
 * @property Carbon|null $expires_at
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Payment[] $payments
 *
 * @package App\Models
 */
class Coupon extends Model
{
	use SoftDeletes;
	protected $table = 'coupons';

	protected $casts = [
		'discount_percentage' => 'int',
		'expires_at' => 'datetime'
	];

	protected $fillable = [
		'code',
		'discount_percentage',
		'expires_at',
		'spaces_id'
	];

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public function space()
	{
		return $this->belongsTo(Space::class, 'spaces_id');
	}
}
