<?php

namespace Tests\Feature;

use App\Models\Table;
use App\Models\Reservation;
use App\Models\TableSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_floor_plan_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->has('tables')
            ->has('upcomingReservations')
        );
    }

    public function test_can_view_tables_on_floor_plan(): void
    {
        $table = Table::factory()->create([
            'table_name' => 'T1',
            'capacity' => 4,
            'status' => 'available',
            'position_x' => 100,
            'position_y' => 100,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('tables.0', fn ($table) => $table
                ->where('table_name', 'T1')
                ->where('capacity', 4)
                ->where('status', 'available')
                ->etc()
            )
        );
    }

    public function test_can_update_table_status(): void
    {
        $table = Table::factory()->create(['status' => 'available']);

        $response = $this->post("/tables/{$table->id}/status", [
            'status' => 'occupied',
            'customer_name' => 'John Doe',
            'pax' => 2,
        ]);

        $response->assertStatus(200);
        
        $table->refresh();
        $this->assertEquals('occupied', $table->status);
        $this->assertNotNull($table->current_session_id);
    }

    public function test_can_create_table_session_when_status_changes_to_occupied(): void
    {
        $table = Table::factory()->create(['status' => 'available']);

        $this->post("/tables/{$table->id}/status", [
            'status' => 'occupied',
            'customer_name' => 'Jane Smith',
            'pax' => 3,
        ]);

        $table->refresh();
        $session = TableSession::find($table->current_session_id);
        
        $this->assertNotNull($session);
        $this->assertEquals('Jane Smith', $session->customer_name);
        $this->assertEquals(3, $session->pax);
        $this->assertNull($session->end_time);
    }

    public function test_can_end_table_session_when_status_changes_from_occupied(): void
    {
        $table = Table::factory()->create(['status' => 'occupied']);
        $session = TableSession::factory()->create([
            'table_id' => $table->id,
            'end_time' => null,
        ]);
        $table->update(['current_session_id' => $session->id]);

        $this->post("/tables/{$table->id}/status", [
            'status' => 'available',
        ]);

        $session->refresh();
        $this->assertNotNull($session->end_time);
        
        $table->refresh();
        $this->assertEquals('available', $table->status);
        $this->assertNull($table->current_session_id);
    }

    public function test_reservations_page_loads_successfully(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create();
        $reservation = Reservation::factory()->create([
            'assigned_table_id' => $table->id,
        ]);

        $response = $this->actingAs($user)->get('/reservations');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('reservations/index')
            ->has('reservations.data')
        );
    }

    public function test_can_create_reservation(): void
    {
        $user = User::factory()->create();
        $table = Table::factory()->create();

        $reservationData = [
            'customer_name' => 'Alice Johnson',
            'customer_phone' => '555-1234',
            'pax' => 4,
            'reservation_time' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'assigned_table_id' => $table->id,
            'status' => 'confirmed',
        ];

        $response = $this->actingAs($user)->post('/reservations', $reservationData);

        $response->assertRedirect('/reservations');
        $this->assertDatabaseHas('reservations', [
            'customer_name' => 'Alice Johnson',
            'customer_phone' => '555-1234',
            'pax' => 4,
            'assigned_table_id' => $table->id,
        ]);
    }

    public function test_can_view_upcoming_reservations_on_floor_plan(): void
    {
        $table = Table::factory()->create();
        $reservation = Reservation::factory()->create([
            'assigned_table_id' => $table->id,
            'reservation_time' => now()->addHours(1),
            'status' => 'confirmed',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('upcomingReservations')
            ->has('upcomingReservations.0', fn ($res) => $res
                ->where('customer_name', $reservation->customer_name)
                ->etc()
            )
        );
    }

    public function test_table_status_validation(): void
    {
        $table = Table::factory()->create();

        $response = $this->post("/tables/{$table->id}/status", [
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function test_reservation_validation(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/reservations', [
            'customer_name' => '',
            'customer_phone' => '',
            'pax' => 0,
            'reservation_time' => 'invalid_date',
            'assigned_table_id' => 999,
        ]);

        $response->assertSessionHasErrors([
            'customer_name',
            'customer_phone',
            'pax',
            'reservation_time',
            'assigned_table_id',
        ]);
    }
}