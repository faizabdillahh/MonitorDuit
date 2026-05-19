<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum to string to support 'recurring' and future values
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('source', 20)->default('manual')->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('source', 20)->default('manual')->change();
        });
    }
};
