<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class TicketType extends Model
{
    use HasFactory;
    protected $table = 'ticket_types';
    protected $fillable = ['event_id', 'name', 'price', 'quantity'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'ticket_type_id');
    }

    public function tickets()
    {
        return $this->hasManyThrough(Ticket::class, OrderItem::class, 'ticket_type_id', 'order_id', 'id', 'order_id');
    }
}
