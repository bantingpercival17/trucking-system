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
        Schema::create('allowance_ranges', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate_from', 10, 2);
            $table->decimal('rate_to', 10, 2);
            $table->decimal('allowance', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_ranges');
    }
};
