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
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->decimal('advance_amount', 10, 2)->default(0)->after('amount');

            $table->decimal('advance_deducted', 10, 2)->default(0)->after('advance_amount');

            $table->dropColumn(['sss_deduction', 'philhealth_deduction', 'pagibig_deduction', 'bonus']);
        });
    }

    public function down(): void
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->decimal('sss_deduction', 10, 2)->default(0);
            $table->decimal('philhealth_deduction', 10, 2)->default(0);
            $table->decimal('pagibig_deduction', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->nullable();

            $table->dropColumn(['advance_amount', 'advance_deducted']);
        });
    }
};
