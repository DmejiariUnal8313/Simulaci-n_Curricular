<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExternalCurriculum;
use App\Models\ExternalSubject;
use App\Models\SubjectConvalidation;

class NotConvalidatedExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test external curriculum that simulates a new curriculum version
        $externalCurriculum = ExternalCurriculum::create([
            'name' => 'Malla Curricular Actualizada 2025 - Ejemplo',
            'institution' => 'Universidad Ejemplo',
            'description' => 'Nueva versión de la malla curricular con materias adicionales y algunas materias que ya no se consideran válidas',
            'status' => 'active',
            'metadata' => [
                'uploaded_at' => now(),
                'file_size' => 2048,
                'original_filename' => 'malla_actualizada_2025.csv'
            ]
        ]);

        // Create external subjects that represent the NEW curriculum
        $externalSubjects = [
            // Core subjects that exist in both curriculums (will be convalidated)
            ['code' => 'MAT101', 'name' => 'Cálculo Diferencial', 'semester' => 1, 'credits' => 4],
            ['code' => 'PRG101', 'name' => 'Fundamentos de Programación', 'semester' => 1, 'credits' => 3],
            ['code' => 'ARQ101', 'name' => 'Arquitectura de Computadores', 'semester' => 2, 'credits' => 3],
            
            // New subjects added to the curriculum (students will need to take these)
            ['code' => 'ETI101', 'name' => 'Ética Profesional en TI', 'semester' => 8, 'credits' => 2],
            ['code' => 'CIB101', 'name' => 'Ciberseguridad Empresarial', 'semester' => 9, 'credits' => 4],
            
            // Old subjects that are no longer valid (students lose these credits)
            ['code' => 'OLD101', 'name' => 'Tecnología Obsoleta', 'semester' => 5, 'credits' => 3],
            ['code' => 'OLD102', 'name' => 'Sistemas Heredados', 'semester' => 6, 'credits' => 2],
            
            // Elective that might be available
            ['code' => 'ELE101', 'name' => 'Electiva Técnica', 'semester' => 10, 'credits' => 2],
        ];

        $createdSubjects = [];
        foreach ($externalSubjects as $subjectData) {
            $subject = ExternalSubject::create([
                'external_curriculum_id' => $externalCurriculum->id,
                'code' => $subjectData['code'],
                'name' => $subjectData['name'],
                'semester' => $subjectData['semester'],
                'credits' => $subjectData['credits'],
                'description' => 'Materia de prueba para demostrar diferentes tipos de convalidación'
            ]);
            $createdSubjects[$subjectData['code']] = $subject;
        }

        // Create convalidations with the correct logic
        
        // DIRECT CONVALIDATIONS: These subjects from the old curriculum 
        // have equivalent subjects in the new curriculum
        SubjectConvalidation::create([
            'external_curriculum_id' => $externalCurriculum->id,
            'external_subject_id' => $createdSubjects['MAT101']->id,
            'internal_subject_code' => '1000004', // Cálculo Diferencial (original curriculum)
            'convalidation_type' => 'direct',
            'equivalence_percentage' => 100.0,
            'status' => 'approved',
            'notes' => 'Contenidos equivalentes entre malla original y nueva'
        ]);

        SubjectConvalidation::create([
            'external_curriculum_id' => $externalCurriculum->id,
            'external_subject_id' => $createdSubjects['PRG101']->id,
            'internal_subject_code' => '4200910', // Fundamentos de Programación (original curriculum)
            'convalidation_type' => 'direct',
            'equivalence_percentage' => 100.0,
            'status' => 'approved',
            'notes' => 'Contenidos equivalentes entre malla original y nueva'
        ]);

        SubjectConvalidation::create([
            'external_curriculum_id' => $externalCurriculum->id,
            'external_subject_id' => $createdSubjects['ARQ101']->id,
            'internal_subject_code' => '4200908', // Arquitectura de Computadores (original curriculum)
            'convalidation_type' => 'direct',
            'equivalence_percentage' => 100.0,
            'status' => 'approved',
            'notes' => 'Contenidos equivalentes entre malla original y nueva'
        ]);

        // NOT CONVALIDATED: These represent subjects that students took in the old curriculum
        // but that NO LONGER COUNT in the new curriculum (lost credits)
        SubjectConvalidation::create([
            'external_curriculum_id' => $externalCurriculum->id,
            'external_subject_id' => $createdSubjects['OLD101']->id,
            'internal_subject_code' => null,
            'convalidation_type' => 'not_convalidated',
            'equivalence_percentage' => 0.0,
            'status' => 'approved',
            'notes' => 'Esta materia ya no es válida en la nueva malla curricular - crédito perdido'
        ]);

        SubjectConvalidation::create([
            'external_curriculum_id' => $externalCurriculum->id,
            'external_subject_id' => $createdSubjects['OLD102']->id,
            'internal_subject_code' => null,
            'convalidation_type' => 'not_convalidated',
            'equivalence_percentage' => 0.0,
            'status' => 'approved',
            'notes' => 'Esta materia ya no es válida en la nueva malla curricular - crédito perdido'
        ]);

        // NEW SUBJECTS: ETI101 and CIB101 are new subjects in the curriculum
        // Students will need to take these (no convalidation needed as they're new)
        
        // FREE ELECTIVE: For future implementation
        SubjectConvalidation::create([
            'external_curriculum_id' => $externalCurriculum->id,
            'external_subject_id' => $createdSubjects['ELE101']->id,
            'internal_subject_code' => null,
            'convalidation_type' => 'free_elective',
            'equivalence_percentage' => 100.0,
            'status' => 'approved',
            'notes' => 'Reconocida como electiva técnica'
        ]);
    }
}
