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
        Schema::create('student_current_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('subject_code', 10);
            $table->string('semester_period', 20); // Ejemplo: "2025-1", "2025-2"
            $table->enum('status', ['cursando', 'en_examen', 'perdida'])->default('cursando');
            $table->decimal('partial_grade', 3, 1)->nullable(); // Nota parcial actual
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_code')->references('code')->on('subjects')->onDelete('cascade');
            
            // Indexes
            $table->index(['student_id', 'semester_period']);
            $table->index('subject_code');
            
            // Unique constraint to avoid duplicates
            $table->unique(['student_id', 'subject_code', 'semester_period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_current_subjects');
    }
};
