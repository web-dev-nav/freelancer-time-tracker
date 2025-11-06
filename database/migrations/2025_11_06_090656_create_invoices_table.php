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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // INV-YYYY-MM-0001
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            // Client Information (copied from project at creation time)
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->text('client_address')->nullable();

            // Invoice Details
            $table->date('invoice_date');
            $table->date('due_date');
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft');

            // Financial Information
            $table->decimal('subtotal', 10, 2)->default(0); // Before tax
            $table->decimal('tax_rate', 5, 2)->default(0); // Tax percentage (e.g., 13.00 for 13%)
            $table->decimal('tax_amount', 10, 2)->default(0); // Calculated tax amount
            $table->decimal('total', 10, 2)->default(0); // Final total

            // Additional Information
            $table->text('notes')->nullable(); // Invoice notes/terms
            $table->text('description')->nullable(); // Work description

            // Email tracking
            $table->timestamp('sent_at')->nullable(); // When invoice was sent
            $table->timestamp('paid_at')->nullable(); // When marked as paid

            $table->timestamps();

            // Indexes
            $table->index('project_id');
            $table->index('status');
            $table->index('invoice_date');
            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
