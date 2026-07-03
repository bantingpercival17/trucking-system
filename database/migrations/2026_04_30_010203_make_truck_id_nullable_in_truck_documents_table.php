<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('truck_documents', function (Blueprint $table) {

            // remove foreign first
            $table->dropForeign(['truck_id']);

        });

        Schema::table('truck_documents', function (Blueprint $table) {

            $table->unsignedBigInteger('truck_id')
                ->nullable()
                ->change();

            $table->foreign('truck_id')
                ->references('id')
                ->on('trucks')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('truck_documents', function (Blueprint $table) {

            $table->dropForeign(['truck_id']);

        });

        Schema::table('truck_documents', function (Blueprint $table) {

            $table->unsignedBigInteger('truck_id')
                ->nullable(false)
                ->change();

            $table->foreign('truck_id')
                ->references('id')
                ->on('trucks')
                ->onDelete('cascade');
        });
    }
};