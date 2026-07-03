<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('watson_payroll_payment_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watson_payroll_payment_id')->constrained('watson_payroll_payments')->cascadeOnDelete();
            $table->foreignId('watson_trip_id')->constrained('watson_trips')->cascadeOnDelete();
            $table->boolean('is_removed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watson_payroll_payment_trips');
    }
};
