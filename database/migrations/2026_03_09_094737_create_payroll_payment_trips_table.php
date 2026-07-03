<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payroll_payment_trips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_payment_id')->constrained()->cascadeOnDelete();

            $table->foreignId('dispatch_trip_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_payment_trips');
    }
};
