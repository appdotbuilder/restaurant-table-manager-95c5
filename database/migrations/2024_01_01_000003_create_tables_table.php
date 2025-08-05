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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->integer('capacity');
            $table->enum('status', ['available', 'occupied', 'reserved', 'cleaning', 'billed'])->default('available');
            $table->decimal('position_x', 8, 2);
            $table->decimal('position_y', 8, 2);
            $table->unsignedBigInteger('current_session_id')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index(['position_x', 'position_y']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};