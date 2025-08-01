# 📊 Sistema de Seeders - Simulación Curricular

## Descripción de Seeders

El sistema utiliza un conjunto específico de seeders para inicializar la base de datos con los datos fundamentales para el funcionamiento de la simulación curricular.

### Seeders Activos

#### 1. 📚 `SubjectSeeder`
**Propósito**: Cargar todas las materias del plan de estudios de Administración de Sistemas de Información

**Datos que carga**:
- 49 materias del plan curricular (semestres 1-10)
- Códigos únicos de materia (ej: 4100548)
- Nombres completos de las materias
- Semestre curricular correspondiente
- Créditos académicos de cada materia

**Ejecución**: Se ejecuta automáticamente en `./docker.sh setup`

#### 2. 🔗 `PrerequisitesSeeder`
**Propósito**: Establecer las dependencias entre materias (prerrequisitos curriculares)

**Datos que carga**:
- 26 materias con prerrequisitos definidos
- Relaciones materia → prerrequisito(s)
- Validaciones de dependencias académicas

**Ejemplos de prerrequisitos clave**:
```php
'4100548' => ['4200916'], // Estructuras de Datos → POO
'4100549' => ['4100548'], // Algoritmos → Estructuras de Datos
'4100553' => ['4100549', '4100555'], // Ing. SW I → Algoritmos + Planeación SI
```

**Ejecución**: Se ejecuta automáticamente en `./docker.sh setup`

### Seeders Desactivados

#### ❌ `StudentSeeder` (Comentado)
**Razón**: Los estudiantes reales se importan desde archivo CSV para mantener la privacidad y usar datos académicos reales.

#### ❌ `StudentCurrentSubjectSeeder` (Comentado)
**Razón**: Las materias actuales se crean automáticamente durante la importación del CSV, separando el historial académico (con notas) de las materias en curso (sin notas).

## Flujo de Inicialización

### Durante `./docker.sh setup`

1. **Migraciones**: Se ejecuta `migrate:fresh` para crear todas las tablas
2. **Seeders básicos**: Se ejecutan los seeders fundamentales:
   ```bash
   php artisan db:seed --class=SubjectSeeder --force
   php artisan db:seed --class=PrerequisitesSeeder --force
   ```
3. **Importación CSV**: Si existe el archivo CSV, se importan los estudiantes reales:
   ```bash
   php artisan import:academic-history "/app/datasets/archivo.csv"
   ```
4. **Cálculo de progreso**: Se actualiza el progreso académico de todos los estudiantes

### Verificación Post-Setup

Puedes verificar que todo se cargó correctamente con:
```bash
./docker.sh db-status
```

Esto mostrará:
- ✅ Cantidad de materias cargadas (debe ser ~49)
- ✅ Cantidad de prerrequisitos cargados (debe ser ~60-70 relaciones)
- ✅ Cantidad de estudiantes importados
- ✅ Cantidad de registros académicos

## Datos Cargados

### Estructura de Materias
- **Total**: 49 materias
- **Distribución**: 10 semestres académicos
- **Créditos**: Variable por materia (2-4 créditos)
- **Códigos**: Sistema institucional (4100xxx, 4200xxx, 1000xxx)

### Estructura de Prerrequisitos
- **Total**: 26 materias con prerrequisitos
- **Relaciones**: ~70 dependencias definidas
- **Tipos**: Prerrequisitos simples y múltiples
- **Validación**: Automática durante inscripciones

### Datos de Estudiantes (CSV)
- **Fuente**: Archivo CSV con datos reales anonimizados
- **Agrupación**: Por número de documento (cédula)
- **Nombres**: Generados aleatoriamente para privacidad
- **Historial**: Materias aprobadas con notas
- **Actuales**: Materias en curso sin notas

## Comandos Útiles

```bash
# Setup completo
./docker.sh setup

# Solo verificar estado
./docker.sh db-status

# Reseeder solo materias
./docker.sh artisan db:seed --class=SubjectSeeder --force

# Reseeder solo prerrequisitos  
./docker.sh artisan db:seed --class=PrerequisitesSeeder --force

# Reimportar estudiantes
./docker.sh artisan import:academic-history "/app/datasets/archivo.csv"

# Recalcular progreso
./docker.sh artisan tinker --execute="echo 'Updated: ' . \App\Models\Student::recalculateAllProgress();"
```

## Importancia de los Prerrequisitos

Los prerrequisitos son fundamentales para:

1. **Validación de inscripciones**: Evitar que estudiantes se inscriban a materias sin cumplir requisitos
2. **Simulaciones curriculares**: Analizar impacto de cambios en las dependencias
3. **Cálculo de progreso**: Determinar qué materias puede cursar cada estudiante
4. **Reportes académicos**: Identificar materias bloqueadas o disponibles

---

🎯 **El sistema está diseñado para funcionar automáticamente con `./docker.sh setup`**
