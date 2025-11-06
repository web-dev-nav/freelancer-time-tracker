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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('client_address');
            $table->string('company_email')->nullable()->after('company_name');
            $table->text('company_address')->nullable()->after('company_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'company_email', 'company_address']);
        });
    }
};
