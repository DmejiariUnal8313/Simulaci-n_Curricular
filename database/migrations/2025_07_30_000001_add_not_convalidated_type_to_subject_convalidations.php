<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing constraint
        DB::statement("ALTER TABLE subject_convalidations DROP CONSTRAINT IF EXISTS subject_convalidations_convalidation_type_check");
        
        // Add the new constraint with the additional value
        DB::statement("ALTER TABLE subject_convalidations ADD CONSTRAINT subject_convalidations_convalidation_type_check CHECK (convalidation_type IN ('direct', 'free_elective', 'not_convalidated'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint
        DB::statement("ALTER TABLE subject_convalidations DROP CONSTRAINT IF EXISTS subject_convalidations_convalidation_type_check");
        
        // Add back the original constraint
        DB::statement("ALTER TABLE subject_convalidations ADD CONSTRAINT subject_convalidations_convalidation_type_check CHECK (convalidation_type IN ('direct', 'free_elective'))");
    }
};
