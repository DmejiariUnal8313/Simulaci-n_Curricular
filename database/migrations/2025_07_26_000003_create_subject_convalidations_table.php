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
        Schema::create('subject_convalidations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_curriculum_id')->constrained()->onDelete('cascade');
            $table->foreignId('external_subject_id')->constrained()->onDelete('cascade');
            $table->string('internal_subject_code')->nullable(); // Código de materia interna (nullable para libre elección)
            $table->enum('convalidation_type', ['direct', 'free_elective']); // Tipo de convalidación
            $table->text('notes')->nullable(); // Notas de la convalidación
            $table->decimal('equivalence_percentage', 5, 2)->default(100.00); // Porcentaje de equivalencia
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('approved_by')->nullable(); // Usuario que aprobó
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('internal_subject_code')->references('code')->on('subjects')->onDelete('cascade');
            $table->unique(['external_subject_id', 'internal_subject_code']); // Una convalidación por materia externa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_convalidations');
    }
};
