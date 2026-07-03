<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->decimal('balance_advance', 10, 2)->default(0)->after('amount');

            $table->date('week_start')->nullable()->after('person_id');

            $table->date('week_end')->nullable()->after('week_start');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->dropColumn(['balance_advance', 'week_start', 'week_end']);
        });
    }
};
