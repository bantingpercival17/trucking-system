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
            $table->date('check_release_date')->nullable()->after('billing_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_trips', function (Blueprint $table) {
            $table->dropColumn('check_release_date');
        });
    }
};
