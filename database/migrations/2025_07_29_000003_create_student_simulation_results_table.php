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
        Schema::create('student_simulation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_version_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->json('original_subjects'); // Materias que tenía originalmente
            $table->json('new_subjects'); // Materias en la nueva malla
            $table->json('convalidated_subjects'); // Materias convalidadas
            $table->json('missing_subjects'); // Materias que le faltan
            $table->decimal('completion_percentage', 5, 2); // Porcentaje de completitud
            $table->integer('credits_validated'); // Créditos convalidados
            $table->integer('credits_remaining'); // Créditos que faltan
            $table->json('impact_analysis'); // Análisis del impacto
            $table->timestamps();
            
            $table->unique(['simulation_version_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_simulation_results');
    }
};
