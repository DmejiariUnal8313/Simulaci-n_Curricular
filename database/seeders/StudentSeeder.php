<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing students
        DB::table('students')->delete();
        DB::table('student_subject')->delete();
        
        // Generate 100 students with realistic names
        $firstNames = [
            'Alejandro', 'Ana', 'Andrés', 'Beatriz', 'Carlos', 'Carmen', 'Daniel', 'Diana', 'Eduardo', 'Elena',
            'Fernando', 'Gabriela', 'Guillermo', 'Isabel', 'Javier', 'Julia', 'Leonardo', 'Laura', 'Manuel', 'María',
            'Miguel', 'Natalia', 'Óscar', 'Paola', 'Pedro', 'Patricia', 'Ricardo', 'Rosa', 'Sebastián', 'Sofía',
            'Antonio', 'Camila', 'Diego', 'Fernanda', 'Gonzalo', 'Luisa', 'Mauricio', 'Mónica', 'Nicolás', 'Pilar',
            'Rafael', 'Sandra', 'Tomás', 'Valeria', 'Víctor', 'Andrea', 'Cristian', 'Daniela', 'Esteban', 'Marcela'
        ];
        
        $lastNames = [
            'García', 'Rodríguez', 'González', 'Fernández', 'López', 'Martínez', 'Sánchez', 'Pérez', 'Gómez', 'Martín',
            'Jiménez', 'Ruiz', 'Hernández', 'Díaz', 'Moreno', 'Álvarez', 'Muñoz', 'Romero', 'Alonso', 'Gutiérrez',
            'Navarro', 'Torres', 'Domínguez', 'Vázquez', 'Ramos', 'Gil', 'Ramírez', 'Serrano', 'Blanco', 'Suárez',
            'Molina', 'Morales', 'Ortega', 'Delgado', 'Castro', 'Ortiz', 'Rubio', 'Marín', 'Sanz', 'Iglesias',
            'Medina', 'Garrido', 'Cortés', 'Castillo', 'Santos', 'Lozano', 'Guerrero', 'Cano', 'Prieto', 'Méndez'
        ];
        
        for ($i = 1; $i <= 100; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName1 = $lastNames[array_rand($lastNames)];
            $lastName2 = $lastNames[array_rand($lastNames)];
            $name = "$firstName $lastName1 $lastName2";
            
            $student = Student::create(['name' => $name]);
            
            // Simulate different student progress levels
            $progressLevel = $this->getRandomProgressLevel();
            $this->enrollStudentWithProgress($student, $progressLevel);
            
            // Calculate and update progress percentage
            $student->updateProgressPercentage();
        }
    }
    
    /**
     * Get a random progress level for student simulation
     */
    private function getRandomProgressLevel(): array
    {
        $levels = [
            // New students (1-2 semesters)
            ['maxSemester' => 2, 'completionRate' => 0.8, 'weight' => 20],
            // Early students (3-4 semesters)
            ['maxSemester' => 4, 'completionRate' => 0.7, 'weight' => 25],
            // Mid students (5-6 semesters)
            ['maxSemester' => 6, 'completionRate' => 0.6, 'weight' => 25],
            // Advanced students (7-8 semesters)
            ['maxSemester' => 8, 'completionRate' => 0.5, 'weight' => 20],
            // Final students (9-10 semesters)
            ['maxSemester' => 10, 'completionRate' => 0.4, 'weight' => 10],
        ];
        
        $totalWeight = array_sum(array_column($levels, 'weight'));
        $random = rand(1, $totalWeight);
        $currentWeight = 0;
        
        foreach ($levels as $level) {
            $currentWeight += $level['weight'];
            if ($random <= $currentWeight) {
                return $level;
            }
        }
        
        return $levels[0]; // fallback
    }
    
    /**
     * Enroll student with realistic progress based on prerequisites
     */
    private function enrollStudentWithProgress(Student $student, array $progressLevel): void
    {
        $allSubjects = Subject::with('prerequisites')->orderBy('semester')->get();
        $completedSubjects = [];
        $failedSubjects = [];
        
        // Process subjects by semester
        for ($semester = 1; $semester <= $progressLevel['maxSemester']; $semester++) {
            $semesterSubjects = $allSubjects->where('semester', $semester);
            
            foreach ($semesterSubjects as $subject) {
                // Check if student can take this subject (prerequisites met)
                if ($this->canTakeSubject($subject, $completedSubjects)) {
                    $shouldComplete = rand(1, 100) <= ($progressLevel['completionRate'] * 100);
                    
                    if ($shouldComplete) {
                        $grade = $this->generateRealisticGrade(true);
                        $status = 'passed';
                        $completedSubjects[] = $subject->code;
                    } else {
                        $grade = $this->generateRealisticGrade(false);
                        $status = 'failed';
                        $failedSubjects[] = $subject->code;
                    }
                    
                    $student->subjects()->attach($subject->code, [
                        'grade' => $grade,
                        'status' => $status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        // Some students might retry failed subjects
        foreach ($failedSubjects as $subjectCode) {
            if (rand(1, 100) <= 30) { // 30% chance to retry
                $subject = $allSubjects->where('code', $subjectCode)->first();
                if ($subject && $this->canTakeSubject($subject, $completedSubjects)) {
                    $grade = $this->generateRealisticGrade(true);
                    $status = 'passed';
                    
                    // Update the existing record
                    $student->subjects()->updateExistingPivot($subjectCode, [
                        'grade' => $grade,
                        'status' => $status,
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
    
    /**
     * Check if student can take a subject based on completed prerequisites
     */
    private function canTakeSubject(Subject $subject, array $completedSubjects): bool
    {
        $prerequisites = $subject->prerequisites->pluck('code')->toArray();
        
        if (empty($prerequisites)) {
            return true;
        }
        
        foreach ($prerequisites as $prereq) {
            if (!in_array($prereq, $completedSubjects)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Generate realistic grades
     */
    private function generateRealisticGrade(bool $shouldPass): float
    {
        if ($shouldPass) {
            // Passed grades: 3.0 to 5.0, weighted towards 3.5-4.0
            $weights = [
                3.0 => 10, 3.1 => 10, 3.2 => 15, 3.3 => 15, 3.4 => 15,
                3.5 => 10, 3.6 => 10, 3.7 => 8, 3.8 => 8, 3.9 => 6,
                4.0 => 5, 4.1 => 4, 4.2 => 3, 4.3 => 3, 4.4 => 2,
                4.5 => 2, 4.6 => 1, 4.7 => 1, 4.8 => 1, 4.9 => 1, 5.0 => 1
            ];
        } else {
            // Failed grades: 0.0 to 2.9, weighted towards 2.0-2.8
            $weights = [
                0.0 => 2, 0.5 => 3, 1.0 => 5, 1.5 => 8, 2.0 => 15,
                2.1 => 15, 2.2 => 15, 2.3 => 12, 2.4 => 10, 2.5 => 8,
                2.6 => 6, 2.7 => 4, 2.8 => 3, 2.9 => 2
            ];
        }
        
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        $currentWeight = 0;
        
        foreach ($weights as $grade => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return (float) $grade;
            }
        }
        
        return $shouldPass ? 3.0 : 2.0; // fallback
    }
}
