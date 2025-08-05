<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Reservation;
use Inertia\Inertia;

class FloorPlanController extends Controller
{
    /**
     * Display the floor plan.
     */
    public function index()
    {
        $tables = Table::with(['currentSession', 'reservations' => function ($query) {
            $query->where('status', 'confirmed')
                  ->where('reservation_time', '>=', now())
                  ->orderBy('reservation_time');
        }])->get();

        $upcomingReservations = Reservation::with('assignedTable')
            ->where('status', 'confirmed')
            ->where('reservation_time', '>=', now())
            ->orderBy('reservation_time')
            ->take(5)
            ->get();

        return Inertia::render('welcome', [
            'tables' => $tables,
            'upcomingReservations' => $upcomingReservations,
        ]);
    }
}