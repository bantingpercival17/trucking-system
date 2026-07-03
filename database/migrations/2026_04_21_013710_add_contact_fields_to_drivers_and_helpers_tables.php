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
            $table->string('contact_number')->nullable()->after('email');
            $table->text('address')->nullable()->after('contact_number');
            $table->string('emergency_contact_person')->nullable()->after('address');
            $table->string('emergency_contact_number')->nullable()->after('emergency_contact_person');
        });

        Schema::table('helpers', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->after('email');
            $table->text('address')->nullable()->after('contact_number');
            $table->string('emergency_contact_person')->nullable()->after('address');
            $table->string('emergency_contact_number')->nullable()->after('emergency_contact_person');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['contact_number', 'address', 'emergency_contact_person', 'emergency_contact_number']);
        });

        Schema::table('helpers', function (Blueprint $table) {
            $table->dropColumn(['contact_number', 'address', 'emergency_contact_person', 'emergency_contact_number']);
        });
    }
};
