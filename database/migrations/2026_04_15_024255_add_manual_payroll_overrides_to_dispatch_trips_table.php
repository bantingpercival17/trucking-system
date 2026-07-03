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
        Schema::table('dispatch_trips', function (Blueprint $table) {
            $table->decimal('manual_amount', 10, 2)->nullable()->after('rate_snapshot');
            $table->decimal('manual_allowance', 10, 2)->nullable()->after('manual_amount');
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_trips', function (Blueprint $table) {
            $table->dropColumn(['manual_amount', 'manual_allowance']);
        });
    }
};
