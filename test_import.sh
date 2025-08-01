#!/bin/bash

# Script para probar la importación de historia académica
# Uso: ./test_import.sh

echo "🚀 PROBANDO IMPORTACIÓN DE HISTORIA ACADÉMICA"
echo "=============================================="

CSV_FILE="datasets/Admon SI - Estudaintes activos con sus asignaturas - RE_MIG_PLA_EST_DATOS.csv"

# Verificar que el archivo existe
if [ ! -f "$CSV_FILE" ]; then
    echo "❌ Error: Archivo CSV no encontrado: $CSV_FILE"
    exit 1
fi

echo "📁 Archivo CSV encontrado: $CSV_FILE"
echo ""

# 1. Primero hacer un dry run para ver qué se importaría
echo "🔍 PASO 1: DRY RUN (análisis sin importar)"
echo "----------------------------------------"
php artisan import:academic-history "$CSV_FILE" --dry-run

echo ""
echo "⏸️  ¿Continuar con la importación real? (y/N): "
read -r response

if [[ "$response" =~ ^[Yy]$ ]]; then
    echo ""
    echo "📥 PASO 2: IMPORTACIÓN REAL"
    echo "---------------------------"
    
    # Hacer backup de la base de datos antes de importar (opcional)
    echo "💾 Creando backup de la base de datos..."
    php artisan db:backup 2>/dev/null || echo "⚠️  Comando backup no disponible, continuando..."
    
    # Importar los datos reales
    php artisan import:academic-history "$CSV_FILE"
    
    echo ""
    echo "🔄 PASO 3: RECALCULANDO PROGRESO DE ESTUDIANTES"
    echo "-----------------------------------------------"
    
    # Recalcular el progreso de todos los estudiantes
    php artisan students:recalculate-progress
    
    echo ""
    echo "📊 PASO 4: ESTADÍSTICAS POST-IMPORTACIÓN"
    echo "----------------------------------------"
    
    # Mostrar estadísticas básicas
    echo "Estudiantes en el sistema:"
    php artisan tinker --execute="echo 'Total estudiantes: ' . App\Models\Student::count() . PHP_EOL;"
    
    echo "Registros académicos:"
    php artisan tinker --execute="echo 'Historial académico: ' . DB::table('student_subject')->count() . PHP_EOL;"
    
    echo "Materias actuales:"
    php artisan tinker --execute="echo 'Materias en curso: ' . App\Models\StudentCurrentSubject::count() . PHP_EOL;"
    
    echo ""
    echo "✅ IMPORTACIÓN COMPLETADA EXITOSAMENTE!"
    echo ""
    echo "📋 PRÓXIMOS PASOS:"
    echo "  1. Verificar los datos en la interfaz web"
    echo "  2. Revisar las materias descartadas (si las hay)"
    echo "  3. Ejecutar análisis de simulación para validar el impacto"
    
else
    echo "❌ Importación cancelada por el usuario"
    exit 0
fi
