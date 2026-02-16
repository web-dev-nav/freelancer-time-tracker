<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_activity_schedules', function (Blueprint $table) {
            $table->string('schedule_type', 20)->default('daily')->after('enabled');
            $table->date('send_date')->nullable()->after('send_time');
            $table->string('working_days')->nullable()->after('send_date');
        });
    }

    public function down(): void
    {
        Schema::table('daily_activity_schedules', function (Blueprint $table) {
            $table->dropColumn(['schedule_type', 'send_date', 'working_days']);
        });
    }
};
