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
        Schema::table('dispatch_trips', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('check_release_date');
            $table->string('check_number')->nullable()->after('bank_name');
        });
    }

    public function down()
    {
        Schema::table('dispatch_trips', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'check_number']);
        });
    }
};
