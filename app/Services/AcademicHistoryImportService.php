<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Subject;
use App\Models\StudentCurrentSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicHistoryImportService
{
    private $validSubjects = [];
    private $invalidSubjectCodes = [];
    private $studentsCache = [];
    private $studentsByDocument = []; // Group records by document
    private $randomNames = [
        'first_names' => [
            'Alejandro', 'Alejandra', 'Andrea', 'Andrés', 'Antonio', 'Carlos', 'Carmen', 'Carolina',
            'Cristian', 'Cristina', 'Daniel', 'Diana', 'Diego', 'Eduardo', 'Elena', 'Fernando',
            'Francisco', 'Gabriela', 'Gustavo', 'Isabel', 'Javier', 'Jessica', 'Jorge', 'José',
            'Juan', 'Juliana', 'Laura', 'Leonardo', 'Luis', 'Luisa', 'Manuel', 'Marcela',
            'Marco', 'María', 'Mario', 'Martha', 'Miguel', 'Natalia', 'Nicole', 'Oscar',
            'Pablo', 'Patricia', 'Paula', 'Pedro', 'Rafael', 'Ricardo', 'Roberto', 'Santiago',
            'Sara', 'Sebastián', 'Sofia', 'Valentina', 'Valeria', 'Víctor'
        ],
        'last_names' => [
            'García', 'González', 'Rodríguez', 'Fernández', 'López', 'Martínez', 'Sánchez', 'Pérez',
            'Gómez', 'Martín', 'Jiménez', 'Ruiz', 'Hernández', 'Díaz', 'Moreno', 'Muñoz',
            'Álvarez', 'Romero', 'Alonso', 'Gutiérrez', 'Navarro', 'Torres', 'Domínguez', 'Vázquez',
            'Ramos', 'Gil', 'Ramírez', 'Serrano', 'Blanco', 'Suárez', 'Molina', 'Morales',
            'Ortega', 'Delgado', 'Castro', 'Ortiz', 'Rubio', 'Marín', 'Sanz', 'Iglesias',
            'Medina', 'Garrido', 'Cortés', 'Castillo', 'Santos', 'Lozano', 'Guerrero', 'Cano'
        ]
    ];
    private $stats = [
        'students' => ['total' => 0, 'created' => 0, 'existing' => 0],
        'subjects' => ['total_records' => 0, 'valid' => 0, 'invalid' => 0, 'invalid_codes' => []],
        'history' => ['created' => 0],
        'current' => ['created' => 0],
        'duplicates' => 0,
        'processing_time' => 0,
        'records_per_second' => 0
    ];

    public function __construct()
    {
        // Don't load subjects in constructor to avoid database queries during boot/migration
        // loadValidSubjects() will be called on demand in importFromCSV()
    }

    /**
     * Load all valid subjects from database
     */
    private function loadValidSubjects()
    {
        $this->validSubjects = Subject::pluck('code')->flip()->toArray();
        Log::info("Loaded " . count($this->validSubjects) . " valid subjects");
    }

    /**
     * Import academic history from CSV file
     */
    public function importFromCSV(string $filePath, bool $dryRun = false): array
    {
        $startTime = microtime(true);
        
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        // Load valid subjects on demand
        if (empty($this->validSubjects)) {
            $this->loadValidSubjects();
        }

        Log::info("Starting academic history import from: {$filePath}");
        Log::info("Mode: " . ($dryRun ? "DRY RUN" : "REAL IMPORT"));

        // Phase 1: Read and group all records by document
        $this->studentsByDocument = $this->readAndGroupRecords($filePath);
        
        $totalStudents = count($this->studentsByDocument);
        $totalRecords = array_sum(array_map('count', $this->studentsByDocument));
        
        Log::info("Phase 1 completed: Found {$totalStudents} unique students with {$totalRecords} total records");
        
        // Phase 2: Process each student and their subjects
        $processedStudents = 0;
        foreach ($this->studentsByDocument as $documento => $studentRecords) {
            if (!$dryRun) {
                $this->processStudent($documento, $studentRecords);
            } else {
                $this->simulateStudent($documento, $studentRecords);
            }
            
            $processedStudents++;
            
            // Progress indicator every 10 students
            if ($processedStudents % 10 === 0) {
                Log::info("Processed {$processedStudents}/{$totalStudents} students...");
            }
        }

        // Calculate performance stats
        $endTime = microtime(true);
        $this->stats['processing_time'] = $endTime - $startTime;
        $this->stats['records_per_second'] = $totalRecords / max($this->stats['processing_time'], 0.001);
        $this->stats['subjects']['invalid_codes'] = array_unique($this->invalidSubjectCodes);

        Log::info("Import completed in " . round($this->stats['processing_time'], 2) . " seconds");
        Log::info("Students: {$this->stats['students']['created']} created, {$this->stats['students']['existing']} existing");
        Log::info("Academic records: {$this->stats['history']['created']} historical, {$this->stats['current']['created']} current");

        return $this->stats;
    }

    /**
     * Read CSV file and group records by document
     */
    private function readAndGroupRecords(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \Exception("Cannot open file: {$filePath}");
        }

        // Read and skip empty lines until we find the header
        $header = null;
        while (($row = fgetcsv($handle)) !== false) {
            if (!$this->isEmptyRow($row)) {
                $header = $row;
                break;
            }
        }

        if ($header === null) {
            throw new \Exception("No valid header found in CSV file");
        }

        // Validate header
        $this->validateHeader($header);
        
        // Get column indexes
        $columnIndexes = $this->getColumnIndexes($header);
        
        $groupedRecords = [];
        $processedRows = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $processedData = $this->processRow($row, $columnIndexes);
            if ($processedData) {
                $documento = $processedData['documento'];
                
                if (!isset($groupedRecords[$documento])) {
                    $groupedRecords[$documento] = [];
                }
                
                $groupedRecords[$documento][] = $processedData;
                $processedRows++;
                
                // Progress indicator every 1000 rows
                if ($processedRows % 1000 === 0) {
                    Log::info("Reading CSV: processed {$processedRows} rows...");
                }
            }
        }

        fclose($handle);
        Log::info("CSV read completed: {$processedRows} valid records from " . count($groupedRecords) . " unique students");
        
        return $groupedRecords;
    }

    /**
     * Process a single student and all their academic records
     */
    private function processStudent(string $documento, array $studentRecords): void
    {
        DB::transaction(function () use ($documento, $studentRecords) {
            // Get or create student with random name
            $student = $this->getOrCreateStudentWithRandomName($documento);
            
            // Process all subjects for this student
            foreach ($studentRecords as $record) {
                if ($record['is_current']) {
                    // This is a current subject (no grade)
                    $this->createCurrentSubject($student, $record);
                } else {
                    // This is historical data (has grade)
                    $this->createHistoricalRecord($student, $record);
                }
            }
        });
    }

    /**
     * Simulate processing a single student (for dry run)
     */
    private function simulateStudent(string $documento, array $studentRecords): void
    {
        // Check if student exists or would be created
        if (!isset($this->studentsCache[$documento])) {
            $existingStudent = Student::where('document', $documento)->first();
            if ($existingStudent) {
                $this->stats['students']['existing']++;
                $this->studentsCache[$documento] = $existingStudent;
            } else {
                $this->stats['students']['created']++;
                $this->stats['students']['total']++;
                $this->studentsCache[$documento] = true; // placeholder
            }
        }

        // Count what would be created for this student
        foreach ($studentRecords as $record) {
            if ($record['is_current']) {
                $this->stats['current']['created']++;
            } else {
                $this->stats['history']['created']++;
            }
        }
    }

    /**
     * Get or create student with random name by document number
     */
    private function getOrCreateStudentWithRandomName(string $documento): Student
    {
        if (isset($this->studentsCache[$documento])) {
            return $this->studentsCache[$documento];
        }

        // Try to find by document number
        $student = Student::where('document', $documento)->first();

        if (!$student) {
            // Generate random name
            $randomName = $this->generateRandomName();
            
            // Create new student
            $student = Student::create([
                'name' => $randomName,
                'document' => $documento,
                'progress_percentage' => 0.00,
            ]);
            
            $this->stats['students']['created']++;
            Log::info("Created new student: {$randomName} (Document: {$documento})");
        } else {
            $this->stats['students']['existing']++;
            Log::info("Found existing student: {$student->name} (Document: {$documento})");
        }

        $this->stats['students']['total'] = max($this->stats['students']['total'], $this->stats['students']['created'] + $this->stats['students']['existing']);
        $this->studentsCache[$documento] = $student;

        return $student;
    }

    /**
     * Generate a random name combining first and last names
     */
    private function generateRandomName(): string
    {
        $firstName = $this->randomNames['first_names'][array_rand($this->randomNames['first_names'])];
        $lastName1 = $this->randomNames['last_names'][array_rand($this->randomNames['last_names'])];
        $lastName2 = $this->randomNames['last_names'][array_rand($this->randomNames['last_names'])];
        
        return "{$firstName} {$lastName1} {$lastName2}";
    }

    /**
     * Validate CSV header
     */
    private function validateHeader(array $header): void
    {
        $requiredColumns = [
            'DOCUMENTO', 'COD_ASIGNATURA', 'NOTA_NUMERICA', 'PERIODO_INSCRIPCION'
        ];

        foreach ($requiredColumns as $column) {
            if (!in_array($column, $header)) {
                throw new \Exception("Required column '{$column}' not found in CSV header");
            }
        }
    }

    /**
     * Get column indexes from header
     */
    private function getColumnIndexes(array $header): array
    {
        return [
            'documento' => array_search('DOCUMENTO', $header),
            'cod_asignatura' => array_search('COD_ASIGNATURA', $header),
            'asignatura' => array_search('ASIGNATURA', $header),
            'nota_numerica' => array_search('NOTA_NUMERICA', $header),
            'nota_alfabetica' => array_search('NOTA_ALFABETICA', $header),
            'periodo_inscripcion' => array_search('PERIODO_INSCRIPCION', $header),
            'creditos' => array_search('CREDITOS', $header),
            'tipo' => array_search('TIPO', $header)
        ];
    }

    /**
     * Check if row is empty
     */
    private function isEmptyRow(array $row): bool
    {
        return empty(array_filter($row, function($value) {
            return !empty(trim($value));
        }));
    }

    /**
     * Process a single CSV row
     */
    private function processRow(array $row, array $indexes): ?array
    {
        $this->stats['subjects']['total_records']++;

        $documento = trim($row[$indexes['documento']] ?? '');
        $codAsignatura = trim($row[$indexes['cod_asignatura']] ?? '');
        $notaNumerica = trim($row[$indexes['nota_numerica']] ?? '');
        $periodoInscripcion = trim($row[$indexes['periodo_inscripcion']] ?? '');

        // Skip if essential data is missing
        if (empty($documento) || empty($codAsignatura) || empty($periodoInscripcion)) {
            return null;
        }

        // Check if subject exists in our system
        if (!isset($this->validSubjects[$codAsignatura])) {
            $this->invalidSubjectCodes[] = $codAsignatura;
            $this->stats['subjects']['invalid']++;
            return null; // Skip this record
        }

        $this->stats['subjects']['valid']++;

        // Determine if this is historical (has grade) or current (no grade)
        $hasGrade = !empty($notaNumerica) && is_numeric($notaNumerica);
        $grade = $hasGrade ? (float) $notaNumerica : null;

        // Determine status based on grade
        $status = 'enrolled'; // default
        if ($hasGrade) {
            $status = $grade >= 3.0 ? 'passed' : 'failed';
        }

        return [
            'documento' => $documento,
            'subject_code' => $codAsignatura,
            'grade' => $grade,
            'status' => $status,
            'periodo_inscripcion' => $periodoInscripcion,
            'is_current' => !$hasGrade,
            'raw_data' => [
                'asignatura' => trim($row[$indexes['asignatura']] ?? ''),
                'nota_alfabetica' => trim($row[$indexes['nota_alfabetica']] ?? ''),
                'creditos' => trim($row[$indexes['creditos']] ?? ''),
                'tipo' => trim($row[$indexes['tipo']] ?? '')
            ]
        ];
    }

    /**
     * Create current subject record
     */
    private function createCurrentSubject(Student $student, array $record): void
    {
        // Check for duplicates in current subjects
        $existing = StudentCurrentSubject::where([
            'student_id' => $student->id,
            'subject_code' => $record['subject_code'],
            'semester_period' => $this->convertPeriod($record['periodo_inscripcion'])
        ])->first();

        if ($existing) {
            $this->stats['duplicates']++;
            return;
        }

        // Also check if this subject already exists in historical records (student_subject)
        $historicalRecord = DB::table('student_subject')
            ->where('student_id', $student->id)
            ->where('subject_code', $record['subject_code'])
            ->where('status', 'passed') // Only check if already passed
            ->first();

        if ($historicalRecord) {
            // Student already passed this subject, don't add as current
            $this->stats['duplicates']++;
            return;
        }

        StudentCurrentSubject::create([
            'student_id' => $student->id,
            'subject_code' => $record['subject_code'],
            'semester_period' => $this->convertPeriod($record['periodo_inscripcion']),
            'status' => 'cursando',
            'partial_grade' => null,
        ]);

        $this->stats['current']['created']++;
    }

    /**
     * Create historical academic record
     */
    private function createHistoricalRecord(Student $student, array $record): void
    {
        // Check if record already exists
        $existing = DB::table('student_subject')
            ->where('student_id', $student->id)
            ->where('subject_code', $record['subject_code'])
            ->first();

        if ($existing) {
            // Update only if new grade is better (higher than existing)
            if ($record['grade'] > $existing->grade) {
                DB::table('student_subject')
                    ->where('student_id', $student->id)
                    ->where('subject_code', $record['subject_code'])
                    ->update([
                        'grade' => $record['grade'],
                        'status' => $record['status'],
                        'updated_at' => now(),
                    ]);
                $this->stats['history']['created']++; // Count as updated
            } else {
                $this->stats['duplicates']++;
            }
            return;
        }

        // Insert new record
        DB::table('student_subject')->insert([
            'student_id' => $student->id,
            'subject_code' => $record['subject_code'],
            'grade' => $record['grade'],
            'status' => $record['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->stats['history']['created']++;
    }

    /**
     * Convert period format from CSV to our format
     */
    private function convertPeriod(string $period): string
    {
        // Convert formats like "2025-1S" to "2025-1"
        return preg_replace('/^(\d{4})-(\d+)[SI]?$/', '$1-$2', $period);
    }

    /**
     * Get import statistics
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Reset statistics
     */
    public function resetStats(): void
    {
        $this->stats = [
            'students' => ['total' => 0, 'created' => 0, 'existing' => 0],
            'subjects' => ['total_records' => 0, 'valid' => 0, 'invalid' => 0, 'invalid_codes' => []],
            'history' => ['created' => 0],
            'current' => ['created' => 0],
            'duplicates' => 0,
            'processing_time' => 0,
            'records_per_second' => 0
        ];
        $this->invalidSubjectCodes = [];
        $this->studentsCache = [];
        $this->studentsByDocument = [];
    }
}
