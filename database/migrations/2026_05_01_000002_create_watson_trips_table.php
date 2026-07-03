<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Mirrors `flash_trips` structure/lifecycle (Draft -> Assigned -> Dispatched -> Completed/Cancelled),
     * including trip_number, payment_status, and billing fields added later for Flash.
     */
    public function up(): void
    {
        Schema::create('watson_trips', function (Blueprint $table) {
            $table->id();

            $table->date('dispatch_date');

            $table->foreignId('destination_id')->constrained('watson_destinations')->cascadeOnDelete();
            $table->foreignId('truck_id')->constrained('trucks')->restrictOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->restrictOnDelete();

            $table->unsignedTinyInteger('trip_number')->nullable();

            $table->string('trip_ticket_no')->nullable();
            $table->string('status')->default('Draft');
            $table->string('payment_status')->default('Unpaid');
            $table->string('billing_status')->nullable();

            $table->date('check_release_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('check_number')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_removed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watson_trips');
    }
};
