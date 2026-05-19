<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3)->default('IDR');
            $table->decimal('rate', 20, 6);
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->unique(['from_currency', 'to_currency']);
            $table->index('fetched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
