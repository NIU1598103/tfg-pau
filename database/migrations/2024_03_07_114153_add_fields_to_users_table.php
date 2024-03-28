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
    {   //TODO: pensar quins camps afegir a la taula USUARI
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('flexible')->default(false);
            $table->boolean('verified')->default(false);
            $table->boolean('admin')->default(false);
            $table->boolean('transplant')->default(false);
            $table->string('type')->nullable();
            $table->string('blocked_days')->nullable();  //hauran d'estar separats per ","
            $table->integer('total_guards')->nullable();
            $table->integer('weekend_guards')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('flexible');
            $table->dropColumn('verified');
            $table->dropColumn('admin');
            $table->dropColumn('transplant');
            $table->dropColumn('type');
            $table->dropColumn('blocked_days');
            $table->dropColumn('total_guards');
            $table->dropColumn('weekend_guards');
        });
    }
};
