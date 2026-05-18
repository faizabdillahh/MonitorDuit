<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('merchant_name', 255)->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->enum('source', ['ai', 'manual'])->default('manual');
            $table->enum('ai_confidence', ['high', 'medium', 'low'])->nullable();
            $table->json('ai_raw_response')->nullable();
            $table->string('receipt_image_path', 500)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'transaction_date']);
            $table->index(['user_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
