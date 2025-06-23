<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            
            // Session tracking
            $table->string('session_id')->unique(); // Unique session identifier
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            
            // Time tracking
            $table->timestamp('clock_in'); // When user started work
            $table->timestamp('clock_out')->nullable(); // When user finished work
            $table->integer('total_minutes')->nullable(); // Total work time in minutes
            
            // Work details
            $table->text('work_description')->nullable(); // What they worked on
            $table->string('project_name')->nullable(); // Optional project categorization
            
            // Metadata
            $table->string('ip_address')->nullable(); // For security tracking
            $table->string('user_agent')->nullable(); // Browser info
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status', 'clock_in']);
            $table->index('clock_in');
            $table->index('session_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_logs');
    }
};