<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('dispatch_trips')) {
            return;
        }

        Schema::table('dispatch_trips', function (Blueprint $table) {
            if (!Schema::hasColumn('dispatch_trips', 'billing_status')) {
                $table->enum('billing_status', ['Billed', 'Pending', 'Unbilled'])->default('Unbilled')->after('status');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('dispatch_trips')) {
            return;
        }

        Schema::table('dispatch_trips', function (Blueprint $table) {
            if (Schema::hasColumn('dispatch_trips', 'billing_status')) {
                $table->dropColumn('billing_status');
            }
        });
    }
};
