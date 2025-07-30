<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubjectConvalidation;
use App\Models\ExternalCurriculum;
use App\Models\ExternalSubject;

class NewSubjectsExampleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     * 
     * This seeder creates a realistic example of how convalidations work:
     * - Direct convalidations: External subjects that match internal subjects
     * - New subjects: External subjects that don't exist in internal curriculum (student must take them)
     * - Lost credits: Internal subjects that student passed but don't exist in external curriculum
     */
    public function run(): void
    {
        // Find the first external curriculum
        $externalCurriculum = ExternalCurriculum::first();
        
        if (!$externalCurriculum) {
            $this->command->info('No external curriculum found. Please create one first.');
            return;
        }

        // Clear existing convalidations for this curriculum
        SubjectConvalidation::where('external_curriculum_id', $externalCurriculum->id)->delete();

        // Get external subjects - load the relationship first
        $externalCurriculum->load('externalSubjects');
        $externalSubjects = $externalCurriculum->externalSubjects;
        
        if ($externalSubjects === null || $externalSubjects->isEmpty()) {
            $this->command->info('No external subjects found in curriculum.');
            return;
        }

        $this->command->info("Creating convalidations for {$externalSubjects->count()} external subjects...");

        // Example scenario: Computer Science curriculum convalidations with REAL codes
        $convalidations = [
            // DIRECT CONVALIDATIONS (subjects that exist in both curricula)
            ['code' => '1000004', 'internal' => '1000004', 'type' => 'direct'],       // Cálculo Diferencial
            ['code' => '1000005', 'internal' => '1000005', 'type' => 'direct'],       // Cálculo Integral
            
            // NEW SUBJECTS (exist only in new curriculum - student must take them)
            ['code' => '4200887', 'internal' => null, 'type' => 'not_convalidated'],  // Introducción a ciencias de computación
            ['code' => '4100810', 'internal' => null, 'type' => 'not_convalidated'],  // Fundamentos de matemáticas
            ['code' => '4100804', 'internal' => null, 'type' => 'not_convalidated'],  // Conjuntos y combinatoria
            ['code' => '4100546', 'internal' => null, 'type' => 'not_convalidated'],  // Programación I (nueva en esta malla)
            ['code' => '4100868', 'internal' => null, 'type' => 'not_convalidated'],  // Sistemas numéricos (nueva)
            
            // FREE ELECTIVES (flexible convalidations)
            ['code' => '4200884', 'internal' => null, 'type' => 'free_elective'],     // Fundamentos de ética
            ['code' => '4200885', 'internal' => null, 'type' => 'free_elective'],     // Universidad y sociedad
        ];

        $created = 0;
        foreach ($convalidations as $conv) {
            // Find the external subject by code
            $externalSubject = $externalSubjects->where('code', $conv['code'])->first();
            
            if ($externalSubject) {
                SubjectConvalidation::create([
                    'external_curriculum_id' => $externalCurriculum->id,
                    'external_subject_id' => $externalSubject->id,
                    'internal_subject_code' => $conv['internal'],
                    'convalidation_type' => $conv['type'],
                    'status' => 'approved',
                    'notes' => $this->getNotesForType($conv['type'], $conv['code']),
                    'equivalence_percentage' => $this->getEquivalencePercentage($conv['type']),
                    'approved_by' => 1,
                    'approved_at' => now()
                ]);
                $created++;
            }
        }

        $this->command->info("Created {$created} convalidations:");
        $this->command->info("- Direct convalidations: Students can skip these subjects");
        $this->command->info("- New subjects: Students must take these additional subjects");
        $this->command->info("- Free electives: Flexible credit recognition");
        $this->command->info("");
        $this->command->info("Note: Subjects from original curriculum that are NOT in the new curriculum");
        $this->command->info("become 'lost credits' (calculated automatically).");
    }

    private function getNotesForType(string $type, string $code): string
    {
        return match($type) {
            'direct' => "Convalidación directa - materia equivalente en ambas mallas",
            'not_convalidated' => "Materia nueva en la malla externa - el estudiante debe cursarla",
            'free_elective' => "Reconocido como crédito electivo",
            default => ''
        };
    }

    private function getEquivalencePercentage(string $type): float
    {
        return match($type) {
            'direct' => 100.00,
            'free_elective' => 50.00,   // Partial credit for electives
            'not_convalidated' => 0.00, // No credit for new subjects
            default => 0.00
        };
    }
}
