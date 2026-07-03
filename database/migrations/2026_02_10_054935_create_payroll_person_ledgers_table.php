
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_person_ledgers', function (Blueprint $table) {
            $table->id();

            $table->date('week_start'); // Monday
            $table->date('week_end');   // Sunday

            $table->enum('person_type', ['driver', 'helper']);
            $table->unsignedBigInteger('person_id'); // driver_id or helper_id

            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();

            // NOTE: no FK to users to avoid your "users.id not found" issue
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->unique(['week_start', 'person_type', 'person_id'], 'payroll_ledger_unique');
            $table->index(['person_type', 'person_id']);
            $table->index('week_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_person_ledgers');
    }
};
