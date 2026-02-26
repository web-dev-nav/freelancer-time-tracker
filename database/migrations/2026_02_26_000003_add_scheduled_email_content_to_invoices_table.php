<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('scheduled_email_subject')->nullable()->after('scheduled_send_at');
            $table->text('scheduled_email_message')->nullable()->after('scheduled_email_subject');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['scheduled_email_subject', 'scheduled_email_message']);
        });
    }
};
