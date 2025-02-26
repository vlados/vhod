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
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->integer('floor');
            $table->decimal('area', 8, 2); // площ в квадратни метри
            $table->integer('rooms');
            $table->enum('status', ['occupied', 'vacant'])->default('occupied');
            $table->timestamps();

            // Номерът на апартамента трябва да е уникален
            $table->unique('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
