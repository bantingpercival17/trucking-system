<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->date('birthday')->nullable()->after('address');
        });

        Schema::table('helpers', function (Blueprint $table) {
            $table->date('birthday')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('birthday');
        });

        Schema::table('helpers', function (Blueprint $table) {
            $table->dropColumn('birthday');
        });
    }
};
