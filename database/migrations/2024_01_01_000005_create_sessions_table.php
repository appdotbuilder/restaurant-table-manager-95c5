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
        Schema::create('sessions_table', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->integer('pax');
            $table->string('customer_name')->nullable();
            $table->timestamps();
            
            $table->index('table_id');
            $table->index('start_time');
            $table->index(['table_id', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions_table');
    }
};