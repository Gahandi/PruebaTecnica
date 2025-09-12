<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Event extends Model
{
    use HasFactory;
    protected $table = 'events';

    protected $fillable = ['name', 'date', 'location'];

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class, 'event_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'event_id');
    }

    public function tickets()
    {
        return $this->hasManyThrough(Ticket::class, Order::class, 'event_id', 'order_id');
    }
}
