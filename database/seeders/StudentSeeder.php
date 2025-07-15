<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample students
        $students = [
            'John Doe',
            'Jane Smith',
            'Carlos Rodriguez',
            'Maria Garcia',
            'David Wilson',
        ];

        foreach ($students as $name) {
            $student = Student::create(['name' => $name]);
            
            // Enroll student in some random subjects
            $subjects = Subject::inRandomOrder()->take(rand(3, 8))->get();
            
            foreach ($subjects as $subject) {
                $grade = rand(20, 50) / 10; // Random grade between 2.0 and 5.0
                $status = $grade >= 3.0 ? 'passed' : 'failed';
                
                $student->subjects()->attach($subject->code, [
                    'grade' => $grade,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
