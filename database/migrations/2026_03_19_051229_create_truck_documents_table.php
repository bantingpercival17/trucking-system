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
        Schema::create('truck_documents', function (Blueprint $table) {
            $table->id();

            // relation
            $table->foreignId('truck_id')->constrained()->cascadeOnDelete();

            // document info
            $table->enum('type', ['ORCR', 'INSURANCE', 'PMS', 'LTFRB']);
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('reminder_days')->default(30);

            // file upload
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();

            // extra
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_documents');
    }
};
