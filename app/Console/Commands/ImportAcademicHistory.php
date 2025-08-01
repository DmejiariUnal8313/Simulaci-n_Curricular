<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AcademicHistoryImportService;

class ImportAcademicHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:academic-history {file_path} {--dry-run : Ejecutar sin guardar cambios} {--limit=1000 : Número máximo de registros a procesar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa historia académica desde archivo CSV con estudiantes y materias reales';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file_path');
        $dryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');

        if (!file_exists($filePath)) {
            $this->error("El archivo no existe: {$filePath}");
            return 1;
        }

        $this->info("==========================================");
        $this->info("IMPORTACIÓN DE HISTORIA ACADÉMICA");
        $this->info("==========================================");
        $this->info("Archivo: {$filePath}");
        $this->info("Modo: " . ($dryRun ? "SIMULACIÓN (dry-run)" : "IMPORTACIÓN REAL"));
        $this->info("Límite: {$limit} registros");
        $this->info("==========================================");

        if (!$dryRun) {
            if (!$this->confirm('¿Está seguro de proceder con la importación real?')) {
                $this->info('Importación cancelada.');
                return 0;
            }
        }

        try {
            $importService = new AcademicHistoryImportService();
            $result = $importService->importFromCsv($filePath, $dryRun, $limit);

            $this->displayResults($result);

            return 0;
        } catch (\Exception $e) {
            $this->error("Error durante la importación: " . $e->getMessage());
            return 1;
        }
    }

    private function displayResults(array $result)
    {
        $this->info("\n==========================================");
        $this->info("RESULTADOS DE LA IMPORTACIÓN");
        $this->info("==========================================");
        
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Registros procesados', number_format($result['total_processed'])],
                ['Estudiantes creados', number_format($result['students_created'])],
                ['Materias creadas', number_format($result['subjects_created'])],
                ['Registros académicos creados', number_format($result['academic_records_created'])],
                ['Materias actuales asignadas', number_format($result['current_subjects_created'])],
                ['Registros duplicados omitidos', number_format($result['duplicates_skipped'])],
                ['Errores', number_format($result['errors'])]
            ]
        );

        if (!empty($result['sample_students'])) {
            $this->info("\nMUESTRA DE ESTUDIANTES PROCESADOS:");
            $this->table(
                ['Documento', 'Nombre', 'Materias Historial', 'Materias Actuales', 'Progreso %'],
                array_map(function($student) {
                    return [
                        $student['document'],
                        substr($student['name'], 0, 30),
                        $student['historical_subjects'],
                        $student['current_subjects'],
                        number_format($student['progress'], 2) . '%'
                    ];
                }, array_slice($result['sample_students'], 0, 10))
            );
        }

        if (!empty($result['errors_detail'])) {
            $this->error("\nERRORES ENCONTRADOS:");
            foreach (array_slice($result['errors_detail'], 0, 10) as $error) {
                $this->error("• {$error}");
            }
        }

        $this->info("\n==========================================");
        if ($result['success']) {
            $this->info("IMPORTACIÓN COMPLETADA EXITOSAMENTE");
        } else {
            $this->error("IMPORTACIÓN COMPLETADA CON ERRORES");
        }
        $this->info("==========================================");
    }
}
