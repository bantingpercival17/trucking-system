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
        Schema::create('truck_destinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('destination_code')->nullable(); // e.g. Store Code, hub name, 
            $table->string('store_name')->nullable();  // e.g. "BAGUIO" or "MAKATI"
            $table->string('area')->nullable();   // e.g. "BENGUET" or "METRO MANILA"
            $table->string('truck_type');         // 6W, 4W, AUV
            $table->decimal('rate', 10, 2);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_destinations');
    }
};
