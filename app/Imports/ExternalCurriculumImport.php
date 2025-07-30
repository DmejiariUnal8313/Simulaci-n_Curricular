<?php

namespace App\Imports;

use App\Models\ExternalSubject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ExternalCurriculumImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    use Importable;

    private $externalCurriculumId;
    private $importedCount = 0;

    public function __construct($externalCurriculumId)
    {
        $this->externalCurriculumId = $externalCurriculumId;
    }

    /**
     * Transform each row into a model.
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['codigo']) || empty($row['nombre'])) {
            return null;
        }

        $this->importedCount++;

        return new ExternalSubject([
            'external_curriculum_id' => $this->externalCurriculumId,
            'code' => $this->cleanString($row['codigo']),
            'name' => $this->cleanString($row['nombre']),
            'credits' => $this->parseCredits($row['creditos'] ?? $row['credito'] ?? 0),
            'semester' => $this->parseSemester($row['semestre'] ?? 1),
            'description' => $this->cleanString($row['descripcion'] ?? ''),
            'additional_data' => [
                'raw_data' => $row,
                'imported_at' => now()
            ]
        ]);
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:50',
            'nombre' => 'required|string|max:500',
            'creditos' => 'nullable|numeric|min:0|max:10',
            'semestre' => 'nullable|integer|min:1|max:15'
        ];
    }

    /**
     * Custom validation messages.
     */
    public function customValidationMessages()
    {
        return [
            'codigo.required' => 'El código de la materia es requerido',
            'nombre.required' => 'El nombre de la materia es requerido',
            'creditos.numeric' => 'Los créditos deben ser un número',
            'semestre.integer' => 'El semestre debe ser un número entero'
        ];
    }

    /**
     * Chunk size for processing large files.
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Clean string values.
     */
    private function cleanString($value)
    {
        if (is_null($value)) {
            return '';
        }
        
        return trim(strip_tags((string) $value));
    }

    /**
     * Parse credits value.
     */
    private function parseCredits($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        // Remove any non-numeric characters except decimal point
        $cleaned = preg_replace('/[^\d.]/', '', (string) $value);
        
        return is_numeric($cleaned) ? (int) $cleaned : 0;
    }

    /**
     * Parse semester value.
     */
    private function parseSemester($value)
    {
        if (is_null($value) || $value === '') {
            return 1;
        }

        // Handle different semester formats
        $cleaned = preg_replace('/[^\d]/', '', (string) $value);
        $semester = is_numeric($cleaned) ? (int) $cleaned : 1;
        
        // Ensure semester is within reasonable bounds
        return max(1, min(15, $semester));
    }

    /**
     * Get the count of imported records.
     */
    public function getImportedCount()
    {
        return $this->importedCount;
    }

    /**
     * Get expected column headers.
     */
    public static function getExpectedHeaders()
    {
        return [
            'codigo' => 'Código de la materia (requerido)',
            'nombre' => 'Nombre de la materia (requerido)',
            'creditos' => 'Número de créditos (opcional)',
            'semestre' => 'Semestre de la materia (opcional)',
            'descripcion' => 'Descripción de la materia (opcional)'
        ];
    }

    /**
     * Get sample Excel structure.
     */
    public static function getSampleData()
    {
        return [
            ['codigo' => 'INF101', 'nombre' => 'Introducción a la Informática', 'creditos' => 3, 'semestre' => 1, 'descripcion' => 'Conceptos básicos de informática'],
            ['codigo' => 'MAT101', 'nombre' => 'Matemáticas I', 'creditos' => 4, 'semestre' => 1, 'descripcion' => 'Álgebra y cálculo básico'],
            ['codigo' => 'PRG101', 'nombre' => 'Programación I', 'creditos' => 4, 'semestre' => 2, 'descripcion' => 'Fundamentos de programación']
        ];
    }
}
