<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['warning', 'exceeded']);
            $table->integer('month');
            $table->integer('year');
            $table->timestamps();

            $table->unique(['budget_id', 'type', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_notification_logs');
    }
};
