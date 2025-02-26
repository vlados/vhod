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
        Schema::create('readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_meter_id')->constrained()->onDelete('cascade');
            $table->decimal('previous_reading', 10, 3); // Предишно показание
            $table->decimal('current_reading', 10, 3); // Текущо показание
            $table->decimal('consumption', 10, 3); // Изчислена консумация (текущо - предишно)
            $table->date('reading_date'); // Дата на отчитане
            $table->foreignId('user_id')->constrained(); // Потребител, въвел показанието
            $table->text('notes')->nullable(); // Бележки
            $table->timestamps();
            
            // Индекс за по-бързо търсене на показания по водомери
            $table->index('water_meter_id');
            // Индекс за търсене на показания по дата
            $table->index('reading_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('readings');
    }
};
