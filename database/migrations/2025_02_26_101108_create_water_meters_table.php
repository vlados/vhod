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
        Schema::create('water_meters', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique(); // Сериен номер, който трябва да е уникален
            $table->enum('type', ['hot', 'cold']); // Тип на водомера (топла или студена вода)
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->date('installation_date'); // Дата на инсталация
            $table->decimal('initial_reading', 10, 3); // Начално показание на водомера
            $table->boolean('is_active')->default(true); // Статус на водомера (активен или не)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_meters');
    }
};
