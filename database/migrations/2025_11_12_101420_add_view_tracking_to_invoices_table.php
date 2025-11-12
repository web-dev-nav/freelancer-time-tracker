<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('view_token')->nullable()->unique()->after('notes');
            $table->timestamp('opened_at')->nullable()->after('view_token');
            $table->unsignedInteger('opened_count')->default(0)->after('opened_at');
            $table->string('opened_ip', 64)->nullable()->after('opened_count');
            $table->string('opened_user_agent', 512)->nullable()->after('opened_ip');
        });

        // Backfill existing invoices with tracking tokens
        DB::table('invoices')
            ->select('id')
            ->whereNull('view_token')
            ->orderBy('id')
            ->chunkById(100, function ($invoices) {
                foreach ($invoices as $invoice) {
                    DB::table('invoices')
                        ->where('id', $invoice->id)
                        ->update(['view_token' => Str::random(40)]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'view_token',
                'opened_at',
                'opened_count',
                'opened_ip',
                'opened_user_agent',
            ]);
        });
    }
};
