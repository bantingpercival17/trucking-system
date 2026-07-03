<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('flash_payroll_payment_trips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('flash_payroll_payment_id')->constrained('flash_payroll_payments')->cascadeOnDelete();

            $table->foreignId('flash_trip_id')->constrained('flash_trips')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_payroll_payment_trips');
    }
};
