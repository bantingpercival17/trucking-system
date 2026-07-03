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
        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_batch_id')->constrained()->cascadeOnDelete();

            $table->string('person_type');
            $table->unsignedBigInteger('person_id');

            $table->integer('total_trips');
            $table->decimal('amount', 10, 2);

            $table->decimal('bonus', 10, 2)->nullable();

            $table->string('payment_mode');
            $table->string('transaction_id')->nullable();

            $table->foreignId('released_by');

            $table->timestamp('paid_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_payments');
    }
};
