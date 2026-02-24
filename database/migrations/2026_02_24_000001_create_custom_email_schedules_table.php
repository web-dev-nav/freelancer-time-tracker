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
        Schema::create('custom_email_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->json('recipients');
            $table->string('schedule_type')->default('date');
            $table->string('send_time')->default('09:00');
            $table->date('send_date')->nullable();
            $table->string('working_days')->nullable();
            $table->boolean('enabled')->default(true);
            $table->string('status')->default('scheduled');
            $table->date('last_sent_date')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'enabled']);
            $table->index(['schedule_type', 'send_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_email_schedules');
    }
};
