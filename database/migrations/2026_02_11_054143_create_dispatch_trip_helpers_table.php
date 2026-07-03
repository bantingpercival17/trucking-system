<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_dispatch_trip_helpers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dispatch_trip_helpers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('dispatch_trip_id');
            $table->unsignedBigInteger('helper_id');

            $table->timestamps();

            $table->unique(['dispatch_trip_id', 'helper_id'], 'trip_helper_unique');
            $table->index('dispatch_trip_id');
            $table->index('helper_id');

            // If you have FK IDs not named "id", avoid FK to prevent issues
            // or add correct FK references if your schema is clean.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_trip_helpers');
    }
};
