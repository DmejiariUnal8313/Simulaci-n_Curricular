# 📚 Sistema de Simulación Curricular - Setup Actualizado

## 🎯 Nuevo Proceso de Configuración

Este documento describe el proceso actualizado para configurar el sistema, que ahora **importa estudiantes reales desde archivos CSV** en lugar de usar datos ficticios generados por seeders.

## 🚀 Inicio Rápido

### 1. Setup Automático Completo

```bash
./docker.sh setup
```

Este comando:
- ✅ Construye y ejecuta los contenedores Docker
- ✅ Ejecuta todas las migraciones de base de datos
- ✅ Carga solo las materias (SubjectSeeder) y prerrequisitos 
- ✅ **NUEVO**: Importa automáticamente estudiantes desde CSV si existe
- ✅ Calcula el progreso académico de todos los estudiantes

### 2. Importación Manual de Estudiantes (si es necesario)

Si el archivo CSV no está en la ubicación esperada durante el setup:

```bash
# Importar estudiantes desde CSV
./docker.sh artisan import:academic-history "/app/datasets/tu-archivo.csv"

# Recalcular progreso de todos los estudiantes
./docker.sh artisan students:recalculate-progress
```

## 📊 Proceso de Importación CSV

### Características del Sistema de Importación:

1. **Agrupación por Cédula**: 
   - Cada número de documento (DOCUMENTO) crea un estudiante único
   - Agrupa todos los registros académicos por cédula

2. **Nombres Aleatorios**:
   - Genera nombres hispanos realistas para proteger la privacidad
   - Combina nombres y apellidos de listas predefinidas

3. **Separación de Datos**:
   - **Historial Académico**: Materias con nota → tabla `student_subject`
   - **Materias Actuales**: Materias sin nota → tabla `student_current_subjects`

4. **Validación de Materias**:
   - Solo importa materias que existen en el sistema
   - Descarta materias no válidas y reporta estadísticas

### Ejemplo de Resultado:
```
📊 IMPORT RESULTS:
─────────────────────────────────────
👥 STUDENTS:
  • Total students processed: 431
  • New students created: 431
  • Existing students found: 0

📚 SUBJECTS:
  • Total subject records: 13,031
  • Valid subjects (in system): 7,204
  • Invalid subjects (discarded): 5,827

📖 ACADEMIC HISTORY:
  • Historical records (with grades): 6,340
  • Current subjects (no grades): 426
  • Duplicate records skipped: 365

⚡ PERFORMANCE:
  • Processing time: 0.14 seconds
  • Records per second: 90,369
```

## 🗄️ Estructura de Base de Datos Actualizada

### Tabla `students` (Actualizada)
```sql
CREATE TABLE students (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,              -- Nombre aleatorio generado
    document VARCHAR(20) UNIQUE,             -- Número de cédula real
    progress_percentage DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Ejemplo de Datos:
| ID | Name | Document | Progress |
|----|------|----------|----------|
| 1 | Juan Rodríguez Ortiz | 1053870333 | 60.48% |
| 2 | Mario Guerrero Serrano | 1007227107 | 49.10% |
| 3 | Cristina Fernández Sanz | 1004561711 | 56.29% |

## 🔧 Comandos Útiles

### Gestión de Contenedores
```bash
./docker.sh start     # Iniciar contenedores
./docker.sh stop      # Detener contenedores  
./docker.sh restart   # Reiniciar contenedores
./docker.sh logs      # Ver logs
./docker.sh shell     # Acceder al contenedor
```

### Gestión de Datos
```bash
# Importar estudiantes desde CSV
./docker.sh artisan import:academic-history "/app/datasets/archivo.csv" --dry-run  # Modo prueba
./docker.sh artisan import:academic-history "/app/datasets/archivo.csv"            # Importación real

# Recalcular progreso académico
./docker.sh artisan students:recalculate-progress

# Verificar estadísticas
./docker.sh artisan tinker --execute="echo 'Estudiantes: ' . \App\Models\Student::count();"
```

### Base de Datos
```bash
./docker.sh db-shell  # Acceder a PostgreSQL
```

## 📁 Ubicación de Archivos CSV

Coloca tus archivos CSV en el directorio:
```
datasets/
└── tu-archivo.csv
```

El archivo debe tener estas columnas mínimas:
- `DOCUMENTO` (cédula del estudiante)
- `COD_ASIGNATURA` (código de la materia)
- `NOTA_NUMERICA` (nota final, vacío para materias actuales)
- `PERIODO_INSCRIPCION` (período académico)

## 🎯 Beneficios del Nuevo Sistema

1. **Datos Reales**: Usa información académica real en lugar de datos ficticios
2. **Privacidad**: Protege la identidad con nombres aleatorios
3. **Rendimiento**: Importa miles de registros en segundos
4. **Flexibilidad**: Soporte para dry-run y validaciones
5. **Automatización**: Setup completamente automatizado
6. **Escalabilidad**: Procesa archivos CSV de cualquier tamaño

## 🔍 Verificación del Setup

Después del setup, verifica que todo esté funcionando:

```bash
# Verificar estudiantes importados
./docker.sh artisan tinker --execute="echo \App\Models\Student::count() . ' estudiantes';"

# Verificar progreso calculado
./docker.sh artisan tinker --execute="echo 'Progreso promedio: ' . \App\Models\Student::avg('progress_percentage') . '%';"

# Acceder a la aplicación
open http://localhost:8080
```

## 📚 Documentación Relacionada

- **Esquema de Base de Datos**: `dbdiagram_schema.dbml`
- **Documentación de Convalidaciones**: `CONVALIDATION_SYSTEM_MANUAL.md`
- **Funcionalidades de Simulación**: `SIMULATION_FEATURES.md`

---

✨ **El sistema está listo para simular cambios curriculares con datos académicos reales!**
