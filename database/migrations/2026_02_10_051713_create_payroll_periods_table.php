<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();

            $table->date('week_start');
            $table->date('week_end');

            $table->decimal('drivers_total', 12, 2)->default(0);
            $table->decimal('helpers_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();

            // FIXED HERE 👇
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['week_start', 'week_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
