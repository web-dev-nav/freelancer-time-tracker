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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('time_log_id')->nullable()->constrained()->onDelete('set null');

            // Item details (can be manual or from time log)
            $table->text('description');
            $table->date('work_date');
            $table->decimal('hours', 8, 2); // Hours worked
            $table->decimal('rate', 8, 2); // Hourly rate
            $table->decimal('amount', 10, 2); // Total amount (hours * rate)

            $table->timestamps();

            // Indexes
            $table->index('invoice_id');
            $table->index('time_log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
