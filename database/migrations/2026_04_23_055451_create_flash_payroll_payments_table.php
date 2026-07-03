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
        Schema::create('flash_payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->integer('total_trips');
            $table->decimal('amount', 10, 2);
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->decimal('advance_deducted', 10, 2)->default(0);
            $table->decimal('balance_advance', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->string('payment_mode');
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_payroll_payments');
    }
};
