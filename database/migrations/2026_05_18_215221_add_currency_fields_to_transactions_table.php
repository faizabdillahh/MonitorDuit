<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('amount_idr', 15, 2)->nullable()->after('total_amount');
            $table->decimal('exchange_rate', 20, 6)->nullable()->after('amount_idr');
            $table->timestamp('rate_fetched_at')->nullable()->after('exchange_rate');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['amount_idr', 'exchange_rate', 'rate_fetched_at']);
        });
    }
};
