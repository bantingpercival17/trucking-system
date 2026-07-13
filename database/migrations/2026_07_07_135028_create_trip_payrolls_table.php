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
        Schema::create('trip_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispatch_id');
            $table->foreign('dispatch_id')->references('id')->on('dispatch_destination_trips')->onDelete('cascade');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('allowance', 12, 2)->default(0);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('payroll_id')->nullable();
            $table->foreign('payroll_id')->references('id')->on('employee_salary_payments')->onDelete('cascade');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_payrolls');
    }
};
