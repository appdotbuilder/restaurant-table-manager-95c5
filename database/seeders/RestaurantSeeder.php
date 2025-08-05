<?php

namespace Database\Seeders;

use App\Models\Table;
use App\Models\Reservation;
use App\Models\TableSession;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create tables in a typical restaurant layout
        $tables = [
            // Front section - 2-person tables
            ['name' => 'T1', 'capacity' => 2, 'x' => 100, 'y' => 100],
            ['name' => 'T2', 'capacity' => 2, 'x' => 200, 'y' => 100],
            ['name' => 'T3', 'capacity' => 2, 'x' => 300, 'y' => 100],
            ['name' => 'T4', 'capacity' => 2, 'x' => 400, 'y' => 100],
            
            // Middle section - 4-person tables
            ['name' => 'T5', 'capacity' => 4, 'x' => 100, 'y' => 250],
            ['name' => 'T6', 'capacity' => 4, 'x' => 300, 'y' => 250],
            ['name' => 'T7', 'capacity' => 4, 'x' => 500, 'y' => 250],
            ['name' => 'T8', 'capacity' => 4, 'x' => 700, 'y' => 250],
            
            // Back section - larger tables
            ['name' => 'T9', 'capacity' => 6, 'x' => 150, 'y' => 400],
            ['name' => 'T10', 'capacity' => 6, 'x' => 450, 'y' => 400],
            ['name' => 'T11', 'capacity' => 8, 'x' => 750, 'y' => 400],
            
            // VIP section
            ['name' => 'VIP1', 'capacity' => 10, 'x' => 200, 'y' => 550],
            ['name' => 'VIP2', 'capacity' => 12, 'x' => 600, 'y' => 550],
        ];

        $createdTables = [];
        foreach ($tables as $tableData) {
            $table = Table::create([
                'table_name' => $tableData['name'],
                'capacity' => $tableData['capacity'],
                'position_x' => $tableData['x'],
                'position_y' => $tableData['y'],
                'status' => 'available',
            ]);
            $createdTables[] = $table;
        }

        // Create some active sessions for demonstration
        $occupiedTables = collect($createdTables)->random(3);
        foreach ($occupiedTables as $table) {
            $session = TableSession::create([
                'table_id' => $table->id,
                'start_time' => now()->subMinutes(random_int(15, 120)),
                'pax' => random_int(1, $table->capacity),
                'customer_name' => fake()->name(),
            ]);
            
            $table->update([
                'status' => 'occupied',
                'current_session_id' => $session->id,
            ]);
        }

        // Set some tables to different statuses
        collect($createdTables)->except($occupiedTables->pluck('id')->toArray())
            ->random(2)->each(function ($table) {
                $table->update(['status' => 'reserved']);
            });

        collect($createdTables)->except($occupiedTables->pluck('id')->toArray())
            ->random(1)->each(function ($table) {
                $table->update(['status' => 'cleaning']);
            });

        // Create some reservations
        $availableTables = collect($createdTables)->where('status', 'available');
        foreach ($availableTables->take(5) as $table) {
            Reservation::create([
                'customer_name' => fake()->name(),
                'customer_phone' => fake()->phoneNumber(),
                'pax' => random_int(1, $table->capacity),
                'reservation_time' => fake()->dateTimeBetween('now', '+7 days'),
                'assigned_table_id' => $table->id,
                'status' => 'confirmed',
            ]);
        }

        // Create additional reservations for today
        foreach (collect($createdTables)->random(3) as $table) {
            Reservation::create([
                'customer_name' => fake()->name(),
                'customer_phone' => fake()->phoneNumber(),
                'pax' => random_int(1, $table->capacity),
                'reservation_time' => now()->addHours(random_int(1, 8)),
                'assigned_table_id' => $table->id,
                'status' => 'confirmed',
            ]);
        }
    }
}