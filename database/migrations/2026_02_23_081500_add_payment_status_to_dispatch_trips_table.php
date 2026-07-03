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
            if (!Schema::hasColumn('dispatch_trips', 'payment_status')) {
                $table->enum('payment_status', ['Paid', 'Unpaid'])->default('Unpaid')->after('billing_status');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('dispatch_trips')) {
            return;
        }

        Schema::table('dispatch_trips', function (Blueprint $table) {
            if (Schema::hasColumn('dispatch_trips', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
