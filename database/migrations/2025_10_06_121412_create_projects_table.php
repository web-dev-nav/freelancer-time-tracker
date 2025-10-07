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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Project/Job name
            $table->string('client_name')->nullable(); // Client or organization name
            $table->string('color')->default('#8b5cf6'); // Color for UI visual distinction
            $table->decimal('hourly_rate', 8, 2)->nullable(); // Optional hourly rate
            $table->enum('status', ['active', 'archived'])->default('active'); // Active or archived
            $table->text('description')->nullable(); // Project description/notes
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
