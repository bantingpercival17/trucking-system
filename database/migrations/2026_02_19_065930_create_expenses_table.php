<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->time('time')->nullable();

            $table->string('plate_number')->nullable();
            $table->decimal('debit', 12, 2);

            $table->enum('receipt_surrendered', ['YES', 'NO'])->nullable();
            $table->string('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
