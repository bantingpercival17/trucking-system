<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Mirrors `destinations` (Owner/Chamonix) since Watson rates are
     * truck-type specific (6W / 4W / AUV) rather than a flat rate like Flash.
     * Adds `origin` to support the Metro-Manila "PMP -> destination" rows
     * from the Watson rate sheet, which the other two companies don't need.
     */
    public function up(): void
    {
        Schema::create('watson_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('origin')->nullable(); // e.g. "PMP" for Metro Manila rows, null for provincial
            $table->string('destination_name')->nullable();   // e.g. "BAGUIO" or "MAKATI"
            $table->string('area')->nullable();   // e.g. "BENGUET" or "METRO MANILA"
            $table->string('truck_type');         // 6W, 4W, AUV
            $table->decimal('rate', 10, 2);
            $table->text('remarks')->nullable();
            $table->boolean('is_removed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watson_destinations');
    }
};
