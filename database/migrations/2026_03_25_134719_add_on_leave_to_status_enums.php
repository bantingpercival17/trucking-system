<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE drivers MODIFY status ENUM('active','inactive','on_trip','on-leave') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE helpers MODIFY status ENUM('active','inactive','on_trip','on-leave') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE drivers MODIFY status ENUM('active','inactive','on_trip') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE helpers MODIFY status ENUM('active','inactive','on_trip') NOT NULL DEFAULT 'active'");
    }
};
