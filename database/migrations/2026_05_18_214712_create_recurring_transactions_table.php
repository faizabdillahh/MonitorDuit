<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('merchant_name')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->text('notes')->nullable();
            $table->enum('frequency', ['weekly', 'biweekly', 'monthly', 'yearly']);
            $table->integer('day_of_month')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_run_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'next_run_date']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
