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
        Schema::create('student_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('subject_code', 10);
            $table->foreign('subject_code')->references('code')->on('subjects')->onDelete('cascade');
            $table->decimal('grade', 3, 1)->nullable(); // Grade with 1 decimal place (0.0 to 5.0)
            $table->enum('status', ['enrolled', 'passed', 'failed', 'withdrawn'])->default('enrolled');
            $table->timestamps();
            
            // Ensure a student can't be enrolled in the same subject twice
            $table->unique(['student_id', 'subject_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_subject');
    }
};
