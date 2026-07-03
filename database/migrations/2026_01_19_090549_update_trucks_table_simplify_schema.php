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
        Schema::table('trucks', function (Blueprint $table) {
            // Only rename if columns exist (for migrations that already had them)
            if (Schema::hasColumn('trucks', 'plate')) {
                $table->renameColumn('plate', 'plate_number');
            }
            if (Schema::hasColumn('trucks', 'type')) {
                $table->renameColumn('type', 'truck_type');
            }

            // Drop unused capacity fields if they exist
            if (Schema::hasColumn('trucks', 'capacity_value')) {
                $table->dropColumn(['capacity_value', 'capacity_unit']);
            }

            // Add status if it doesn't exist
            if (!Schema::hasColumn('trucks', 'status')) {
                $table
                    ->enum('status', ['active', 'inactive'])
                    ->default('active')
                    ->after('truck_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trucks', function (Blueprint $table) {
            // Revert column names
            $table->renameColumn('plate_number', 'plate');
            $table->renameColumn('truck_type', 'type');

            // Restore capacity fields
            $table->decimal('capacity_value', 10, 2)->after('plate');
            $table->string('capacity_unit', 10)->after('capacity_value');

            // Drop status
            $table->dropColumn('status');
        });
    }
};
