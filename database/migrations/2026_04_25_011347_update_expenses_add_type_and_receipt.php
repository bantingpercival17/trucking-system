<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('type')->default('fuel')->after('plate_number');
            $table->string('receipt_image')->nullable()->after('receipt_surrendered');
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['type', 'receipt_image']);
        });
    }
};
