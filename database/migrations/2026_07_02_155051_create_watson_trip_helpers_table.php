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
        Schema::create('watson_trip_helpers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispatch_trip_id')->nullable();
            $table->foreign('dispatch_trip_id')->references('id')->on('watson_trips');
            $table->unsignedBigInteger('helper_id')->nullable();
            $table->foreign('helper_id')->references('id')->on('helpers');
            $table->unique(['dispatch_trip_id', 'helper_id'], 'trip_helper_unique');
            $table->index('dispatch_trip_id');
            $table->index('helper_id');
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_removed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watson_trip_helpers');
    }
};
