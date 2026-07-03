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
        Schema::create('flash_payrolls', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('driver_id');
            $table->date('week_start');
            $table->date('week_end');

            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);

            $table->enum('status', ['Unpaid','Pending', 'Paid'])->default('Unpaid');

            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('drivers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_payrolls');
    }
};
