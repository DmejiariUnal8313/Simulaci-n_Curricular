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
        // Create a test external curriculum
        $externalCurriculum = ExternalCurriculum::create([
            'name' => 'Malla con Materias Adicionales - Test',
            'institution' => 'Universidad Ejemplo',
            'description' => 'Ejemplo que demuestra el uso de materias adicionales (no convalidadas)',
            'status' => 'active',
            'metadata' => [
                'uploaded_at' => now(),
                'file_size' => 2048,
                'original_filename' => 'test_additional_subjects.csv'
            ]
        ]);

        // Create external subjects with some that will be marked as "not_convalidated"
        $externalSubjects = [
            // Regular subjects that can be convalidated
            ['code' => 'MAT101', 'name' => 'Cálculo I', 'semester' => 1, 'credits' => 4],
            ['code' => 'PRG101', 'name' => 'Programación I', 'semester' => 1, 'credits' => 3],
            
            // Additional subjects that will be "not_convalidated" (requirements for the new curriculum)
            ['code' => 'ETI101', 'name' => 'Ética Profesional', 'semester' => 2, 'credits' => 2],
            ['code' => 'GER101', 'name' => 'Gestión de Proyectos', 'semester' => 6, 'credits' => 3],
            ['code' => 'SEG101', 'name' => 'Seguridad Informática Avanzada', 'semester' => 8, 'credits' => 4],
            ['code' => 'INN101', 'name' => 'Innovación y Emprendimiento', 'semester' => 9, 'credits' => 3],
            
            // Free elective
            ['code' => 'ART101', 'name' => 'Arte Digital', 'semester' => 7, 'credits' => 2],
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

        // Create convalidations - direct convalidations
        SubjectConvalidation::create([
            'external_curriculum_id' => $externalCurriculum->id,
            'external_subject_id' => $createdSubjects['MAT101']->id,
            'internal_subject_code' => '1000004', // Cálculo Diferencial
            'convalidation_type' => 'direct',
            'equivalence_percentage' => 100.0,
            'status' => 'approved',
            'notes' => 'Convalidación directa por equivalencia de contenidos'
        ]);

        SubjectConvalidation::create([
            'external_curriculum_id' => $externalCurriculum->id,
            'external_subject_id' => $createdSubjects['PRG101']->id,
            'internal_subject_code' => '4200910', // Fundamentos de Programación
            'convalidation_type' => 'direct',
            'equivalence_percentage' => 100.0,
            'status' => 'approved',
            'notes' => 'Convalidación directa por equivalencia de contenidos'
        ]);

        // Create "not_convalidated" convalidations (additional requirements)
        $additionalSubjects = ['ETI101', 'GER101', 'SEG101', 'INN101'];
        foreach ($additionalSubjects as $code) {
            if (isset($createdSubjects[$code])) {
                SubjectConvalidation::create([
                    'external_curriculum_id' => $externalCurriculum->id,
                    'external_subject_id' => $createdSubjects[$code]->id,
                    'internal_subject_code' => null,
                    'convalidation_type' => 'not_convalidated',
                    'equivalence_percentage' => 0.0,
                    'status' => 'approved',
                    'notes' => 'Materia adicional requerida para estudiantes que migren a esta malla'
                ]);
            }
        }

        // Create free elective
        SubjectConvalidation::create([
            'external_curriculum_id' => $externalCurriculum->id,
            'external_subject_id' => $createdSubjects['ART101']->id,
            'internal_subject_code' => null,
            'convalidation_type' => 'free_elective',
            'equivalence_percentage' => 100.0,
            'status' => 'approved',
            'notes' => 'Convalidada como electiva libre'
        ]);
    }
}
