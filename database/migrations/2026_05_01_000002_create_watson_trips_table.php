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
            // Employee ID (Driver or Helper)
            $table->unsignedBigInteger('employee_id');
            // Identify whether the employee is a driver or helper
            $table->enum('employee_type', ['driver', 'helper']);
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('allowance', 12, 2)->default(0);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->enum('status', ['Draft', 'Approved', 'Paid'])
                ->default('Draft');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'employee_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watson_trips');
    }
};
