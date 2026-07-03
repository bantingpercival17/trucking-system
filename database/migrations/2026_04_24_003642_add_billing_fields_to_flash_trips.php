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
        Schema::table('flash_trips', function (Blueprint $table) {
            $table->string('billing_status')->nullable();
            $table->date('check_release_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('check_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flash_trips', function (Blueprint $table) {
            //
        });
    }
};
