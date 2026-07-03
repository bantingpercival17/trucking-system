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
            $table->unsignedTinyInteger('trip_number')->nullable()->after('driver_id');
        });
    }

    public function down()
    {
        Schema::table('flash_trips', function (Blueprint $table) {
            $table->dropColumn('trip_number');
        });
    }
};
