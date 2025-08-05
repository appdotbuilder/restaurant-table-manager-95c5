<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Table>
 */
class TableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'table_name' => 'Table ' . fake()->unique()->numberBetween(1, 50),
            'capacity' => fake()->numberBetween(2, 8),
            'status' => fake()->randomElement(['available', 'occupied', 'reserved', 'cleaning']),
            'position_x' => fake()->randomFloat(2, 50, 950),
            'position_y' => fake()->randomFloat(2, 50, 650),
        ];
    }

    /**
     * Indicate that the table is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    /**
     * Indicate that the table is occupied.
     */
    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'occupied',
        ]);
    }
}