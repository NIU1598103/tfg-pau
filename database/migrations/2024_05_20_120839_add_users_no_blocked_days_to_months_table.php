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
        Schema::table('months', function (Blueprint $table) {
            $table->json('users_no_blocked_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('months', function (Blueprint $table) {
            $table->dropColumn('users_no_blocked_days');
        });
    }
};
