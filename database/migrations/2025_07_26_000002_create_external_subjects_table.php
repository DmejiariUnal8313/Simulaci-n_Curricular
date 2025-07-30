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
        Schema::create('external_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_curriculum_id')->constrained()->onDelete('cascade');
            $table->string('code'); // Código de la materia externa
            $table->string('name'); // Nombre de la materia externa
            $table->integer('credits'); // Créditos de la materia
            $table->integer('semester'); // Semestre en la malla externa
            $table->text('description')->nullable(); // Descripción adicional
            $table->json('additional_data')->nullable(); // Datos adicionales del Excel
            $table->timestamps();
            
            $table->unique(['external_curriculum_id', 'code']); // Un código por malla
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_subjects');
    }
};
