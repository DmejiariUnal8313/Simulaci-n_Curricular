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
        Schema::create('simulation_versions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la versión de simulación
            $table->text('description')->nullable(); // Descripción de los cambios
            $table->json('curriculum_changes'); // JSON con los cambios realizados
            $table->enum('status', ['draft', 'active', 'exported'])->default('draft');
            $table->timestamp('exported_at')->nullable(); // Cuando se exportó a convalidación
            $table->foreignId('external_curriculum_id')->nullable()->constrained()->onDelete('set null'); // Relación con convalidación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_versions');
    }
};
