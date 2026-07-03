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
        // 🔥 STEP 1: FIX DATA FIRST
        DB::table('drivers')
            ->where('status', 'on_trip')
            ->update([
                'status' => 'active',
                'availability_status' => 'on_trip',
            ]);

        DB::table('helpers')
            ->where('status', 'on_trip')
            ->update([
                'status' => 'active',
                'availability_status' => 'on_trip',
            ]);

        // 🔥 STEP 2: ALTER ENUM
        DB::statement("ALTER TABLE drivers MODIFY status ENUM('active','inactive','on_leave') DEFAULT 'active'");
        DB::statement("ALTER TABLE helpers MODIFY status ENUM('active','inactive','on_leave') DEFAULT 'active'");

        // 🔥 STEP 3: FIX availability_status ENUM
        DB::statement("ALTER TABLE drivers MODIFY availability_status ENUM('available','unavailable','on_trip') DEFAULT 'available'");
        DB::statement("ALTER TABLE helpers MODIFY availability_status ENUM('available','unavailable','on_trip') DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people_tables', function (Blueprint $table) {
            //
        });
    }
};
