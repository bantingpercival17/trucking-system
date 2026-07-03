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
        Schema::table('expenses', function (Blueprint $table) {
            // Add after odometer for cleaner structure
            $table->decimal('distance', 10, 2)->nullable()->after('odometer');
            $table->decimal('km_per_liter', 10, 2)->nullable()->after('distance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['distance', 'km_per_liter']);
        });
    }
};