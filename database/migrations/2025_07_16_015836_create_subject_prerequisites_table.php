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
        Schema::create('subject_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code', 10); // The subject that requires prerequisites
            $table->string('prerequisite_code', 10); // The prerequisite subject
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('subject_code')->references('code')->on('subjects')->onDelete('cascade');
            $table->foreign('prerequisite_code')->references('code')->on('subjects')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate prerequisites
            $table->unique(['subject_code', 'prerequisite_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_prerequisites');
    }
};
