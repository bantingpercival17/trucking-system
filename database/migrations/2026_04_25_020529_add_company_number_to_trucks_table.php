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
        Schema::table('trucks', function (Blueprint $table) {
            $table->string('company_number')->nullable()->after('plate_number');
        });
    }

    public function down()
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->dropColumn('company_number');
        });
    }
};
