<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->decimal('sss_deduction', 10, 2)->default(0)->after('balance_advance');
            $table->decimal('philhealth_deduction', 10, 2)->default(0)->after('sss_deduction');
            $table->decimal('pagibig_deduction', 10, 2)->default(0)->after('philhealth_deduction');
            $table->decimal('final_amount', 10, 2)->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            //
        });
    }
};
