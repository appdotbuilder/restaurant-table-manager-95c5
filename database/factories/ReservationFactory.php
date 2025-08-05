<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'pax' => fake()->numberBetween(1, 8),
            'reservation_time' => fake()->dateTimeBetween('now', '+30 days'),
            'assigned_table_id' => Table::factory(),
            'status' => fake()->randomElement(['confirmed', 'seated', 'completed']),
        ];
    }

    /**
     * Indicate that the reservation is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the reservation is for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'reservation_time' => fake()->dateTimeBetween('today', 'tomorrow'),
        ]);
    }
}