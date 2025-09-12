<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 50, 1000);
        $taxes = $total * 0.16;
        
        return [
            'id' => $this->faker->uuid(),
            'user_id' => \App\Models\User::factory(),
            'event_id' => \App\Models\Event::factory(),
            'coupon_id' => null,
            'total' => $total + $taxes,
            'taxes' => $taxes,
        ];
    }
}
