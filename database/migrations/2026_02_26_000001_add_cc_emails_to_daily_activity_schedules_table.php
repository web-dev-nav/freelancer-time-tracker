<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_activity_schedules', function (Blueprint $table) {
            $table->string('cc_emails', 2000)->nullable()->after('activity_columns');
        });
    }

    public function down(): void
    {
        Schema::table('daily_activity_schedules', function (Blueprint $table) {
            $table->dropColumn('cc_emails');
        });
    }
};
