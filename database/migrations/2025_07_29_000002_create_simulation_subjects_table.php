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
        Schema::create('simulation_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_version_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique(); // Código único de la materia
            $table->string('name'); // Nombre de la materia
            $table->integer('credits')->default(3); // Créditos
            $table->integer('semester'); // Semestre
            $table->text('description')->nullable(); // Descripción
            $table->json('prerequisites')->nullable(); // Array de códigos de prerrequisitos
            $table->enum('change_type', ['new', 'modified', 'moved', 'unchanged']); // Tipo de cambio
            $table->string('original_code')->nullable(); // Código original si fue modificado
            $table->timestamps();
            
            $table->index(['simulation_version_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_subjects');
    }
};
