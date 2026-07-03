<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('flash_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('hub_code')->nullable(); // Optional HUB code
            $table->string('area')->nullable(); // HUB
            $table->decimal('rate', 10, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_destinations');
    }
};
