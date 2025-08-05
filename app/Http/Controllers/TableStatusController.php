<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\TableSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TableStatusController extends Controller
{
    /**
     * Update table status and handle sessions.
     */
    public function store(Request $request, Table $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved,cleaning,billed',
            'customer_name' => 'nullable|string|max:255',
            'pax' => 'nullable|integer|min:1',
        ]);

        $newStatus = $validated['status'];
        $currentStatus = $table->status;

        // Handle session management based on status changes
        if ($newStatus === 'occupied' && $currentStatus !== 'occupied') {
            // Start new session
            $session = TableSession::create([
                'table_id' => $table->id,
                'start_time' => now(),
                'pax' => $validated['pax'] ?? 1,
                'customer_name' => $validated['customer_name'],
            ]);
            
            $table->update([
                'status' => $newStatus,
                'current_session_id' => $session->id,
            ]);
        } elseif ($newStatus !== 'occupied' && $currentStatus === 'occupied') {
            // End current session
            if ($table->current_session_id) {
                $session = TableSession::find($table->current_session_id);
                if ($session && !$session->end_time) {
                    $session->update(['end_time' => now()]);
                }
            }
            
            $table->update([
                'status' => $newStatus,
                'current_session_id' => null,
            ]);
        } else {
            // Simple status update
            $table->update(['status' => $newStatus]);
        }

        // Return updated floor plan data
        $tables = Table::with(['currentSession', 'reservations' => function ($query) {
            $query->where('status', 'confirmed')
                  ->where('reservation_time', '>=', now())
                  ->orderBy('reservation_time');
        }])->get();

        $upcomingReservations = \App\Models\Reservation::with('assignedTable')
            ->where('status', 'confirmed')
            ->where('reservation_time', '>=', now())
            ->orderBy('reservation_time')
            ->take(5)
            ->get();

        return Inertia::render('welcome', [
            'tables' => $tables,
            'upcomingReservations' => $upcomingReservations,
            'success' => 'Table status updated successfully.',
        ]);
    }
}