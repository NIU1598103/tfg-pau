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
    { //TODO: pensar que mes aqui
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('id_guardTransplant')->nullable();
            $table->unsignedBigInteger('id_ref')->nullable();
            $table->unsignedBigInteger('id_refLoc')->nullable();
            $table->integer('day');
            $table->integer('month');
            $table->integer('year');
            $table->integer('week_day');
            $table->integer('prueba')->nullable();
            $table->foreignId('month_id')->constrained('months')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
