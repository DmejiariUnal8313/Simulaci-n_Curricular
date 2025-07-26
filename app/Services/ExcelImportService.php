<?php

namespace App\Services;

use App\Models\ExternalCurriculum;
use App\Models\ExternalSubject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExcelImportService
{
    /**
     * Import curriculum from Excel/CSV file
     */
    public function importCurriculum(UploadedFile $file, $curriculumId)
    {
        $extension = $file->getClientOriginalExtension();
        $importedCount = 0;

        if (in_array($extension, ['csv'])) {
            $importedCount = $this->importFromCSV($file, $curriculumId);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            $importedCount = $this->importFromExcel($file, $curriculumId);
        } else {
            throw new \Exception('Formato de archivo no soportado. Use CSV, XLS o XLSX.');
        }

        return $importedCount;
    }

    /**
     * Import from CSV file
     */
    private function importFromCSV(UploadedFile $file, $curriculumId)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Read header row
        $importedCount = 0;

        // Validate header
        $requiredColumns = ['codigo', 'nombre'];
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $header)) {
                throw new \Exception("Columna requerida '{$column}' no encontrada en el archivo.");
            }
        }

        // Get column indexes
        $codeIndex = array_search('codigo', $header);
        $nameIndex = array_search('nombre', $header);
        $creditsIndex = array_search('creditos', $header);
        $semesterIndex = array_search('semestre', $header);
        $descriptionIndex = array_search('descripcion', $header);

        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty($row[$codeIndex]) || empty($row[$nameIndex])) {
                continue;
            }

            $subject = ExternalSubject::create([
                'external_curriculum_id' => $curriculumId,
                'code' => trim($row[$codeIndex]),
                'name' => trim($row[$nameIndex]),
                'credits' => $creditsIndex !== false ? (int)($row[$creditsIndex] ?? 0) : 0,
                'semester' => $semesterIndex !== false ? (int)($row[$semesterIndex] ?? 1) : 1,
                'description' => $descriptionIndex !== false ? trim($row[$descriptionIndex] ?? '') : '',
                'additional_data' => [
                    'raw_data' => $row,
                    'imported_at' => now()
                ]
            ]);

            $importedCount++;
        }

        fclose($handle);
        return $importedCount;
    }

    /**
     * Import from Excel file (basic implementation)
     */
    private function importFromExcel(UploadedFile $file, $curriculumId)
    {
        // For now, we'll ask users to save as CSV
        // This is a simplified approach until we can install proper Excel support
        throw new \Exception('Para archivos Excel, por favor guarde el archivo como CSV y vuelva a intentar.');
    }

    /**
     * Generate sample CSV content
     */
    public static function getSampleCSV()
    {
        return "codigo,nombre,creditos,semestre,descripcion\n" .
               "INF101,Introducción a la Informática,3,1,Conceptos básicos de informática\n" .
               "MAT101,Matemáticas I,4,1,Álgebra y cálculo básico\n" .
               "PRG101,Programación I,4,2,Fundamentos de programación\n" .
               "PRG102,Programación II,4,3,Programación avanzada\n" .
               "BD101,Bases de Datos I,3,4,Fundamentos de bases de datos";
    }

    /**
     * Validate file format and size
     */
    public function validateFile(UploadedFile $file)
    {
        $maxSize = 10 * 1024 * 1024; // 10MB
        $allowedTypes = ['text/csv', 'application/csv', 'text/plain'];
        $allowedExtensions = ['csv'];

        if ($file->getSize() > $maxSize) {
            throw new \Exception('El archivo es demasiado grande. El tamaño máximo es 10MB.');
        }

        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Tipo de archivo no válido. Por favor use archivos CSV.');
        }

        return true;
    }
}
