<?php

use App\Http\Controllers\FloorPlanController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TableStatusController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

// Main floor plan view
Route::get('/', [FloorPlanController::class, 'index'])->name('home');

// Table status updates (can be accessed without auth for staff use)
Route::post('/tables/{table}/status', [TableStatusController::class, 'store'])->name('tables.status.store');

// Resource routes for authenticated users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    
    // Full CRUD for tables (admin functionality)
    Route::resource('tables', TableController::class);
    
    // Reservations management
    Route::resource('reservations', ReservationController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
