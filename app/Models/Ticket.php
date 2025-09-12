<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tickets';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'order_id', 'ticket_type_id', 'qr_code', 'qr_url', 'used'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!isset($model->id) || $model->id === null) {
                $model->id = Str::uuid();
            }
        });
    }

    protected $casts = [
        'used' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    public function checkin(): HasOne
    {
        return $this->hasOne(Checkin::class, 'ticket_id');
    }
}
