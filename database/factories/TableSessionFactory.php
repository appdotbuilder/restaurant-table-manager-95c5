<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TableSession>
 */
class TableSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-2 hours', 'now');
        
        return [
            'table_id' => Table::factory(),
            'start_time' => $startTime,
            'end_time' => fake()->optional(0.7)->dateTimeBetween($startTime, 'now'),
            'pax' => fake()->numberBetween(1, 8),
            'customer_name' => fake()->optional(0.8)->name(),
        ];
    }

    /**
     * Indicate that the session is active (no end time).
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_time' => null,
        ]);
    }
}