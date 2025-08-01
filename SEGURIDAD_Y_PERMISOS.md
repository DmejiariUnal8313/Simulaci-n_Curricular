# Seguridad y Permisos del Proyecto

## Protección de Datos Confidenciales

### Archivos CSV Excluidos del Control de Versiones

Para proteger la información académica confidencial, se ha configurado `.gitignore` para excluir:

```gitignore
# Dataset confidencial - información académica real
/datasets/
datasets/
*.csv

# Archivos temporales de importación
/storage/import_temp/
import_temp/
```

### Archivos Protegidos

- **`datasets/`**: Directorio completo con datos académicos reales
- **`*.csv`**: Todos los archivos CSV del proyecto
- **`/storage/import_temp/`**: Archivos temporales de importación

## Permisos de Ejecución

### Scripts Shell del Proyecto

El proyecto incluye varios scripts shell que requieren permisos de ejecución:

```bash
# Scripts principales
./docker.sh                 # Script principal de gestión Docker
./import_academic_history.sh # Importación de historial académico
./test_import.sh            # Pruebas de importación local
./test_import_docker.sh     # Pruebas de importación en Docker

# Scripts del sistema Docker
./docker/init_db.sh         # Inicialización de base de datos
./docker/php/entrypoint.sh  # Punto de entrada del contenedor PHP
```

### Configuración Automática de Permisos

El script `./docker.sh` incluye comandos para configurar automáticamente todos los permisos:

```bash
# Dar permisos automáticamente durante el setup
./docker.sh setup

# O dar permisos específicamente
./docker.sh permissions
```

### Configuración Manual

Si necesitas configurar permisos manualmente:

```bash
chmod +x ./docker.sh
chmod +x ./docker/init_db.sh
chmod +x ./docker/php/entrypoint.sh
chmod +x ./import_academic_history.sh
chmod +x ./test_import.sh
chmod +x ./test_import_docker.sh
```

## Comandos del Script Docker

```bash
# Configuración inicial completa
./docker.sh setup           # Incluye permisos + build + migrate + seed

# Gestión de permisos
./docker.sh permissions     # Solo configurar permisos de scripts

# Gestión de contenedores
./docker.sh start           # Iniciar contenedores
./docker.sh stop            # Detener contenedores
./docker.sh restart         # Reiniciar contenedores

# Acceso y logs
./docker.sh shell           # Acceder al contenedor de aplicación
./docker.sh db-shell        # Acceder al contenedor de base de datos
./docker.sh logs [service]  # Ver logs (opcional: especificar servicio)

# Comandos Laravel y desarrollo
./docker.sh artisan [cmd]   # Ejecutar comandos artisan
./docker.sh composer [cmd]  # Ejecutar comandos composer
./docker.sh npm [cmd]       # Ejecutar comandos npm
```

## Seguridad de Datos

### Datos Reales vs Datos de Prueba

- **Datos reales**: Archivo CSV con información académica confidencial
  - Ubicación: `datasets/Admon SI - Estudaintes activos con sus asignaturas - RE_MIG_PLA_EST_DATOS.csv`
  - **Protección**: Excluido del control de versiones
  - **Privacidad**: Nombres reemplazados por nombres aleatorios durante importación

- **Datos de prueba**: Seeders con datos sintéticos
  - Para desarrollo y pruebas
  - Incluidos en el control de versiones

### Flujo de Importación Seguro

1. **Lectura**: El CSV se lee pero nunca se commitea
2. **Agrupación**: Se agrupa por número de documento (cédula)
3. **Anonimización**: Se asignan nombres aleatorios
4. **Validación**: Solo se importan materias válidas existentes
5. **Separación**: Historial (con notas) vs materias actuales (sin notas)

## Verificación de Seguridad

Para verificar que los datos están protegidos:

```bash
# Verificar que el CSV está siendo ignorado
git check-ignore -v "datasets/Admon SI - Estudaintes activos con sus asignaturas - RE_MIG_PLA_EST_DATOS.csv"

# Verificar permisos de scripts
ls -la *.sh docker/*.sh

# Ver archivos ignorados
git status --ignored
```

## Buenas Prácticas

1. **Nunca commitear** archivos CSV con datos reales
2. **Siempre verificar** permisos antes de ejecutar scripts
3. **Usar nombres aleatorios** para proteger identidad de estudiantes
4. **Mantener separados** datos reales de datos de prueba
5. **Documentar cambios** en seguridad y permisos

---

**Fecha de actualización**: 31 de Julio, 2025  
**Responsable**: Sistema de protección de datos académicos
