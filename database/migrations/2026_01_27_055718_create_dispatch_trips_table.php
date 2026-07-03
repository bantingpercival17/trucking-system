<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dispatch_trips', function (Blueprint $table) {
            $table->id();
            $table->string('trip_ticket_no')->unique();
            $table->date('dispatch_date');
            $table->time('dispatch_time')->nullable();

            // FK columns
            $table->foreignId('destination_id')->constrained('destinations')->restrictOnDelete();
            $table->foreignId('truck_id')->constrained('trucks')->restrictOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->restrictOnDelete();
            $table->foreignId('helper_id')->nullable()->constrained('helpers')->nullOnDelete();

            $table->decimal('rate_snapshot', 12, 2)->nullable();

            $table->enum('status', ['Draft', 'Assigned', 'Dispatched', 'Completed', 'Cancelled'])->default('Draft');
            $table->text('remarks')->nullable();
            $table->dateTime('dispatched_at')->nullable();
            $table->unsignedBigInteger('dispatched_by')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();

            $table->timestamps();

            $table->index(['dispatch_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_trips');
    }
};
