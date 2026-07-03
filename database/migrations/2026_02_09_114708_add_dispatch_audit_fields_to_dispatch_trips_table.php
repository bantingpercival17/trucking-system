<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dispatch_trips', function (Blueprint $table) {
            // Only add if they don't exist already
            if (!Schema::hasColumn('dispatch_trips', 'dispatched_at')) {
                $table->timestamp('dispatched_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('dispatch_trips', 'dispatched_by')) {
                $table->unsignedBigInteger('dispatched_by')->nullable()->after('dispatched_at');
            }
            if (!Schema::hasColumn('dispatch_trips', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('dispatched_by');
            }
            if (!Schema::hasColumn('dispatch_trips', 'completed_by')) {
                $table->unsignedBigInteger('completed_by')->nullable()->after('completed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_trips', function (Blueprint $table) {
            // If you added FK, drop them first.
            // $table->dropForeign(['dispatched_by']);
            // $table->dropForeign(['completed_by']);

            $table->dropColumn(['dispatched_at', 'dispatched_by', 'completed_at', 'completed_by']);
        });
    }
};
