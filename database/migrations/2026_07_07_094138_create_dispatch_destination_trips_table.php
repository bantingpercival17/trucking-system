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
        Schema::create('dispatch_destination_trips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedBigInteger('destination_id');
            $table->foreign('destination_id')->references('id')->on('truck_destinations')->onDelete('cascade');
            /* Driver */
            $table->unsignedBigInteger('driver_id');
            $table->foreign('driver_id')->references('id')->on('employees')->onDelete('cascade');
            /* Helpers */
            $table->unsignedBigInteger('helper_first_id')->nullable();
            $table->foreign('helper_first_id')->nullable()->references('id')->on('employees')->onDelete('cascade');
            $table->unsignedBigInteger('helper_second_id')->nullable();
            $table->foreign('helper_second_id')->nullable()->references('id')->on('employees')->onDelete('cascade');
            $table->unsignedBigInteger('truck_id')->nullable();
            $table->foreign('truck_id')->nullable()->references('id')->on('trucks')->onDelete('cascade');

            $table->date('dispatch_date');
            $table->unsignedTinyInteger('trip_number')->nullable();
            $table->string('trip_ticket_no')->nullable()->unique();
            $table->enum('dispatch_status', ['Draft', 'Assigned', 'Dispatched', 'Completed'])->default('Draft');
            $table->boolean('is_driver_paid')->nullable(); // true = payroll generated, false = not generated
            $table->boolean('is_helper_first_paid')->nullable(); // true = payroll generated, false = not generated
            $table->boolean('is_helper_second_paid')->nullable(); // true = payroll generated, false = not generated
            $table->boolean('billing_status')->nullable(); // true = billed, false = not billed, null = not applicable

            $table->date('check_release_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('check_number')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_destination_trips');
    }
};
