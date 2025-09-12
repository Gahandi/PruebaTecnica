<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'order_id' => \App\Models\Order::factory(),
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . $this->faker->uuid(),
            'used' => false,
        ];
    }
}
