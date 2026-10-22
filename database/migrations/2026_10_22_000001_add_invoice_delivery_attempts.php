<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedTinyInteger('scheduled_send_attempts')->default(0);
            $table->text('last_send_error')->nullable();
            $table->timestamp('next_send_attempt_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', fn (Blueprint $table) => $table->dropColumn(['scheduled_send_attempts', 'last_send_error', 'next_send_attempt_at']));
    }
};
