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
        Schema::create('student_convalidations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_convalidation_id')->constrained()->onDelete('cascade');
            $table->decimal('external_grade', 4, 2); // Nota obtenida en la materia externa
            $table->decimal('internal_grade', 4, 2)->nullable(); // Nota convertida al sistema interno
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Notas del administrador
            $table->string('processed_by')->nullable(); // Usuario que procesó
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'subject_convalidation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_convalidations');
    }
};
