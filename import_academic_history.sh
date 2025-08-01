#!/bin/bash

# Script para importar historia académica usando Docker
# Uso: ./import_academic_history.sh [archivo_csv] [opciones]

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para imprimir mensajes con color
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
    echo -e "${BLUE} IMPORTACIÓN HISTORIA ACADÉMICA${NC}"
    echo -e "${BLUE}================================${NC}"
}

# Verificar argumentos
if [ $# -eq 0 ]; then
    print_error "Se requiere especificar el archivo CSV"
    echo "Uso: $0 <archivo_csv> [--dry-run] [--limit=N]"
    echo ""
    echo "Ejemplos:"
    echo "  $0 datasets/historia_academica.csv --dry-run"
    echo "  $0 datasets/historia_academica.csv --limit=500"
    echo "  $0 datasets/historia_academica.csv"
    exit 1
fi

CSV_FILE="$1"
DOCKER_CSV_PATH="/app/$CSV_FILE"
shift

# Verificar que el archivo existe
if [ ! -f "$CSV_FILE" ]; then
    print_error "Archivo no encontrado: $CSV_FILE"
    exit 1
fi

print_header
print_status "Archivo CSV: $CSV_FILE"
print_status "Ruta Docker: $DOCKER_CSV_PATH"

# Verificar que Docker está corriendo
if ! docker-compose ps | grep -q "simulacion_curricular_app"; then
    print_warning "El contenedor de la aplicación no está corriendo"
    print_status "Iniciando contenedores..."
    docker-compose up -d
    
    # Esperar a que la aplicación esté lista
    print_status "Esperando a que la aplicación esté lista..."
    sleep 10
fi

# Construir comando con opciones adicionales
ARTISAN_COMMAND="import:academic-history $DOCKER_CSV_PATH"

# Agregar opciones si se proporcionan
for arg in "$@"; do
    case $arg in
        --dry-run)
            ARTISAN_COMMAND="$ARTISAN_COMMAND --dry-run"
            print_status "Modo DRY RUN activado"
            ;;
        --limit=*)
            LIMIT="${arg#*=}"
            ARTISAN_COMMAND="$ARTISAN_COMMAND --limit=$LIMIT"
            print_status "Límite establecido: $LIMIT registros"
            ;;
        *)
            print_warning "Opción desconocida: $arg"
            ;;
    esac
done

print_status "Comando a ejecutar: php artisan $ARTISAN_COMMAND"
echo ""

# Ejecutar comando en Docker
print_status "Ejecutando importación..."
docker-compose exec app php artisan $ARTISAN_COMMAND

IMPORT_RESULT=$?

echo ""
if [ $IMPORT_RESULT -eq 0 ]; then
    print_status "✅ Importación completada exitosamente"
    
    # Si no es dry-run, sugerir siguiente paso
    if [[ "$*" != *"--dry-run"* ]]; then
        echo ""
        print_status "💡 SIGUIENTES PASOS RECOMENDADOS:"
        echo "   1. Verificar progreso de estudiantes:"
        echo "      docker-compose exec app php artisan students:recalculate-progress"
        echo ""
        echo "   2. Revisar base de datos:"
        echo "      docker-compose exec app php artisan tinker"
        echo ""
        echo "   3. Acceder a la aplicación:"
        echo "      http://localhost:8080"
    fi
else
    print_error "❌ Error durante la importación (código: $IMPORT_RESULT)"
    exit $IMPORT_RESULT
fi
