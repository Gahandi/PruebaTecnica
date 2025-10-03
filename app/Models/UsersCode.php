<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UsersCode
 * 
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property bool $used
 * @property Carbon $expires_at
 * @property string $type
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class UsersCode extends Model
{
	use SoftDeletes;
	protected $table = 'users_code';

	protected $casts = [
		'user_id' => 'int',
		'used' => 'bool',
		'expires_at' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'code',
		'used',
		'expires_at',
		'type'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
