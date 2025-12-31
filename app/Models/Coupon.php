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
 * @property int|null $max_uses
 * @property int $uses_count
 * @property int|null $max_uses_per_user
 * @property float|null $min_order_amount
 * @property bool $is_active
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
		'expires_at' => 'datetime',
		'max_uses' => 'int',
		'uses_count' => 'int',
		'max_uses_per_user' => 'int',
		'min_order_amount' => 'decimal:2',
		'is_active' => 'boolean'
	];

	protected $fillable = [
		'code',
		'discount_percentage',
		'expires_at',
		'spaces_id',
		'max_uses',
		'uses_count',
		'max_uses_per_user',
		'min_order_amount',
		'is_active'
	];

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public function space()
	{
		return $this->belongsTo(Space::class, 'spaces_id');
	}

	/**
	 * Verificar si el cupón es válido para usar
	 */
	public function isValid(?int $userId = null, float $orderAmount = 0): array
	{
		// Verificar si está activo
		if (!$this->is_active) {
			return ['valid' => false, 'message' => 'El cupón está desactivado'];
		}

		// Verificar expiración
		if ($this->expires_at && $this->expires_at->isPast()) {
			return ['valid' => false, 'message' => 'El cupón ha expirado'];
		}

		// Verificar límite de usos totales
		if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
			return ['valid' => false, 'message' => 'El cupón ha alcanzado su límite de usos'];
		}

		// Verificar monto mínimo
		if ($this->min_order_amount !== null && $orderAmount < $this->min_order_amount) {
			return ['valid' => false, 'message' => "El monto mínimo de compra es $" . number_format($this->min_order_amount, 2)];
		}

		// Verificar límite por usuario
		if ($userId !== null && $this->max_uses_per_user !== null) {
			$userUses = Payment::where('coupon_id', $this->id)
				->whereHas('order', function ($q) use ($userId) {
					$q->where('user_id', $userId);
				})
				->count();

			if ($userUses >= $this->max_uses_per_user) {
				return ['valid' => false, 'message' => 'Ya has usado este cupón el número máximo de veces permitido'];
			}
		}

		return ['valid' => true, 'message' => 'Cupón válido'];
	}

	/**
	 * Incrementar el contador de usos
	 */
	public function incrementUsage(): void
	{
		$this->increment('uses_count');
	}

	/**
	 * Obtener el badge de estado
	 */
	public function getStatusBadgeAttribute(): string
	{
		if (!$this->is_active) {
			return 'inactive';
		}
		if ($this->expires_at && $this->expires_at->isPast()) {
			return 'expired';
		}
		if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
			return 'exhausted';
		}
		return 'active';
	}
}
