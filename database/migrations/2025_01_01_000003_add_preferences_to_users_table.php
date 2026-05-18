<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('dark_mode_preference', ['system', 'light', 'dark'])->default('system')->after('password');
            $table->string('timezone', 50)->default('Asia/Jakarta')->after('dark_mode_preference');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dark_mode_preference', 'timezone']);
        });
    }
};
