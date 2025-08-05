<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Http\Requests\UpdateTableStatusRequest;
use App\Models\Table;
use App\Models\TableSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tables = Table::with(['currentSession', 'reservations'])->get();
        
        return Inertia::render('tables/index', [
            'tables' => $tables
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('tables/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTableRequest $request)
    {
        $table = Table::create($request->validated());

        return redirect()->route('tables.index')
            ->with('success', 'Table created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        $table->load(['currentSession', 'reservations', 'sessions' => function ($query) {
            $query->orderBy('start_time', 'desc')->take(10);
        }]);

        return Inertia::render('tables/show', [
            'table' => $table
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table)
    {
        return Inertia::render('tables/edit', [
            'table' => $table
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTableRequest $request, Table $table)
    {
        $table->update($request->validated());

        return redirect()->route('tables.index')
            ->with('success', 'Table updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        // End any active session before deleting
        if ($table->current_session_id) {
            $session = TableSession::find($table->current_session_id);
            if ($session) {
                $session->update(['end_time' => now()]);
            }
        }

        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Table deleted successfully.');
    }
}