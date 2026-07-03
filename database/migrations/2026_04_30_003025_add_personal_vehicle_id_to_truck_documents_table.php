<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('truck_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('personal_vehicle_id')
                ->nullable()
                ->after('truck_id');

            $table->foreign('personal_vehicle_id')
                ->references('id')
                ->on('personal_vehicles')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('truck_documents', function (Blueprint $table) {
            $table->dropForeign(['personal_vehicle_id']);
            $table->dropColumn('personal_vehicle_id');
        });
    }
};
