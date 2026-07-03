<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('flash_trips', function (Blueprint $table) {
            $table->id();

            $table->date('dispatch_date');

            // 🔗 relationships
            $table->foreignId('destination_id')->constrained('flash_destinations')->cascadeOnDelete();
            $table->foreignId('truck_id')->constrained('trucks')->restrictOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->restrictOnDelete();

            // 📄 trip info
            $table->string('trip_ticket_no')->nullable();
            $table->string('status')->default('Draft'); // Draft, Assigned, Dispatched, Completed

            $table->text('remarks')->nullable();

            // ⏱ timestamps for lifecycle
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_trips');
    }
};
