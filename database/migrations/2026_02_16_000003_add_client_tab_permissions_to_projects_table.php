<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('client_can_access_dashboard')->default(true)->after('client_user_id');
            $table->boolean('client_can_access_history')->default(true)->after('client_can_access_dashboard');
            $table->boolean('client_can_access_reports')->default(true)->after('client_can_access_history');
            $table->boolean('client_can_access_invoices')->default(true)->after('client_can_access_reports');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'client_can_access_dashboard',
                'client_can_access_history',
                'client_can_access_reports',
                'client_can_access_invoices',
            ]);
        });
    }
};
