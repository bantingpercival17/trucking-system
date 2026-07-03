<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // trucks
        DB::statement("ALTER TABLE trucks MODIFY status ENUM('active','inactive','on_trip') NOT NULL DEFAULT 'active'");
        // drivers
        DB::statement("ALTER TABLE drivers MODIFY status ENUM('active','inactive','on_trip') NOT NULL DEFAULT 'active'");
        // helpers
        DB::statement("ALTER TABLE helpers MODIFY status ENUM('active','inactive','on_trip') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE trucks MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE drivers MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE helpers MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
    }
};
