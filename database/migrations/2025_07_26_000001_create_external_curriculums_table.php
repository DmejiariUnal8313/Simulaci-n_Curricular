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
        Schema::create('external_curriculums', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la malla externa (ej: "Universidad XYZ - Ingeniería de Sistemas")
            $table->string('institution')->nullable(); // Institución de origen
            $table->text('description')->nullable(); // Descripción de la malla
            $table->string('uploaded_file')->nullable(); // Ruta del archivo Excel original
            $table->json('metadata')->nullable(); // Metadata adicional
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_curriculums');
    }
};
