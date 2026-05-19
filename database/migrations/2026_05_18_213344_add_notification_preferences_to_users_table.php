<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notif_budget_warning')->default(true)->after('timezone');
            $table->boolean('notif_budget_exceeded')->default(true)->after('notif_budget_warning');
            $table->boolean('notif_reminder_email')->default(true)->after('notif_budget_exceeded');
            $table->integer('reminder_idle_days')->default(3)->after('notif_reminder_email');
            $table->timestamp('last_reminded_at')->nullable()->after('reminder_idle_days');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notif_budget_warning',
                'notif_budget_exceeded',
                'notif_reminder_email',
                'reminder_idle_days',
                'last_reminded_at',
            ]);
        });
    }
};
