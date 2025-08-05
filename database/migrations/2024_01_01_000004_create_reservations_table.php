<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->integer('pax');
            $table->datetime('reservation_time');
            $table->foreignId('assigned_table_id')->constrained('tables')->onDelete('cascade');
            $table->enum('status', ['confirmed', 'seated', 'cancelled', 'completed'])->default('confirmed');
            $table->timestamps();
            
            $table->index('reservation_time');
            $table->index('status');
            $table->index(['reservation_time', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};