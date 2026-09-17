<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_activity_schedules', function (Blueprint $table) {
            $table->unsignedTinyInteger('failed_attempts')->default(0)->after('last_sent_date');
            $table->dateTime('last_attempt_at')->nullable()->after('failed_attempts');
        });

        Schema::table('custom_email_schedules', function (Blueprint $table) {
            $table->unsignedTinyInteger('failed_attempts')->default(0)->after('last_sent_date');
            $table->dateTime('last_attempt_at')->nullable()->after('failed_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('daily_activity_schedules', function (Blueprint $table) {
            $table->dropColumn(['failed_attempts', 'last_attempt_at']);
        });

        Schema::table('custom_email_schedules', function (Blueprint $table) {
            $table->dropColumn(['failed_attempts', 'last_attempt_at']);
        });
    }
};
