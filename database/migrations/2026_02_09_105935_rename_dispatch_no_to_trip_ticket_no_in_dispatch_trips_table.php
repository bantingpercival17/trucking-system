<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dispatch_trips', function (Blueprint $table) {
            // Only rename if dispatch_no column exists
            if (Schema::hasColumn('dispatch_trips', 'dispatch_no')) {
                $table->renameColumn('dispatch_no', 'trip_ticket_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_trips', function (Blueprint $table) {
             $table->renameColumn('trip_ticket_no', 'dispatch_no');
        });
    }
};
