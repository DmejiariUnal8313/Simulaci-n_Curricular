#!/bin/bash

# Script de prueba para verificar la importación de historia académica
# Uso: ./test_import_docker.sh

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}================================${NC}"
    echo -e "${BLUE} PRUEBA IMPORTACIÓN CON DOCKER${NC}"
    echo -e "${BLUE}================================${NC}"
}

CSV_FILE="datasets/Admon SI - Estudaintes activos con sus asignaturas - RE_MIG_PLA_EST_DATOS.csv"

print_header

# 1. Verificar que el archivo CSV existe
print_status "1. Verificando archivo CSV..."
if [ ! -f "$CSV_FILE" ]; then
    print_error "Archivo CSV no encontrado: $CSV_FILE"
    exit 1
fi
print_status "✅ Archivo CSV encontrado"

# 2. Verificar Docker Compose
print_status "2. Verificando Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    print_error "docker-compose no está instalado"
    exit 1
fi
print_status "✅ Docker Compose disponible"

# 3. Verificar estado de contenedores
print_status "3. Verificando contenedores..."
if ! docker-compose ps | grep -q "Up"; then
    print_warning "Contenedores no están corriendo. Iniciando..."
    docker-compose up -d
    sleep 15
    print_status "✅ Contenedores iniciados"
else
    print_status "✅ Contenedores ya están corriendo"
fi

# 4. Verificar conexión a la base de datos
print_status "4. Verificando conexión a base de datos..."
if docker-compose exec app php artisan migrate:status > /dev/null 2>&1; then
    print_status "✅ Conexión a base de datos OK"
else
    print_warning "Ejecutando migraciones..."
    docker-compose exec app php artisan migrate --force
    print_status "✅ Migraciones ejecutadas"
fi

# 5. Verificar que existen materias en la base de datos
print_status "5. Verificando materias en base de datos..."
SUBJECT_COUNT=$(docker-compose exec app php artisan tinker --execute="echo App\\Models\\Subject::count();")
if [ "$SUBJECT_COUNT" -gt 0 ]; then
    print_status "✅ Se encontraron $SUBJECT_COUNT materias en la base de datos"
else
    print_warning "No hay materias en la base de datos. Ejecutando seeders..."
    docker-compose exec app php artisan db:seed --class=SubjectSeeder
    print_status "✅ Materias insertadas"
fi

# 6. Ejecutar DRY RUN de la importación
print_status "6. Ejecutando DRY RUN de importación (solo 100 registros)..."
echo ""
docker-compose exec app php artisan import:academic-history "/app/$CSV_FILE" --dry-run --limit=100

echo ""
print_status "✅ DRY RUN completado exitosamente"

# 7. Preguntar si ejecutar importación real
echo ""
print_warning "¿Desea ejecutar la importación REAL? (solo 100 registros de prueba)"
print_warning "Esto MODIFICARÁ la base de datos."
read -p "Continuar? (y/N): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "7. Ejecutando importación REAL (100 registros)..."
    echo ""
    docker-compose exec app php artisan import:academic-history "/app/$CSV_FILE" --limit=100
    
    echo ""
    print_status "✅ Importación REAL completada"
    
    # 8. Verificar resultados
    print_status "8. Verificando resultados..."
    
    STUDENT_COUNT=$(docker-compose exec app php artisan tinker --execute="echo App\\Models\\Student::count();")
    HISTORY_COUNT=$(docker-compose exec app php artisan tinker --execute="echo DB::table('student_subject')->count();")
    CURRENT_COUNT=$(docker-compose exec app php artisan tinker --execute="echo App\\Models\\StudentCurrentSubject::count();")
    
    echo ""
    print_status "📊 RESULTADOS:"
    echo "   • Estudiantes en base de datos: $STUDENT_COUNT"
    echo "   • Registros de historial académico: $HISTORY_COUNT"
    echo "   • Materias actuales: $CURRENT_COUNT"
    
    echo ""
    print_status "💡 COMANDOS ÚTILES:"
    echo "   • Ver estudiantes: docker-compose exec app php artisan tinker --execute=\"App\\Models\\Student::with('subjects')->take(3)->get()\""
    echo "   • Recalcular progreso: docker-compose exec app php artisan students:recalculate-progress"
    echo "   • Acceder aplicación: http://localhost:8080"
    
else
    print_status "Importación cancelada por el usuario"
fi

echo ""
print_status "🎉 Prueba completada exitosamente"
