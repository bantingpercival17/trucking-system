<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {

            $table->string('store_code')->after('id');
            $table->string('store_name')->after('store_code');
            $table->string('area')->nullable()->after('store_name');

            $table->string('truck_type')->after('area'); // ex: 6W, L300

            $table->decimal('rate', 10, 2)->change(); // keep but standardize

            $table->text('remarks')->nullable()->after('rate');
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn([
                'store_code',
                'store_name',
                'area',
                'truck_type',
                'remarks',
            ]);
        });
    }
};
