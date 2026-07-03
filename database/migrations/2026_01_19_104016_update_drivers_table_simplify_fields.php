<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Drop unused columns if they exist
            if (Schema::hasColumn('drivers', 'age')) {
                $table->dropColumn('age');
            }
            if (Schema::hasColumn('drivers', 'license_number')) {
                $table->dropColumn('license_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Restore dropped columns
            $table->unsignedInteger('age')->after('name');
            $table->string('license_number')->unique()->after('age');
        });
    }
};
