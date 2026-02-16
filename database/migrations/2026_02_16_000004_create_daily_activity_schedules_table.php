<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_activity_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('client_email')->unique();
            $table->string('client_name')->nullable();
            $table->boolean('enabled')->default(false);
            $table->string('send_time', 5)->default('18:00');
            $table->date('last_sent_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_activity_schedules');
    }
};
