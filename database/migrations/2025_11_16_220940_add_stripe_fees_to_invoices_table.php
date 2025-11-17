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
            $table->boolean('stripe_fees_included')->default(false)->after('tax_amount');
            $table->decimal('stripe_fee_amount', 10, 2)->default(0)->after('stripe_fees_included');
            $table->decimal('stripe_fee_percentage', 5, 2)->default(2.9)->after('stripe_fee_amount');
            $table->decimal('stripe_fee_fixed', 10, 2)->default(0.30)->after('stripe_fee_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['stripe_fees_included', 'stripe_fee_amount', 'stripe_fee_percentage', 'stripe_fee_fixed']);
        });
    }
};
