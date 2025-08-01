# 🚀 Setup Rápido - Simulación Curricular

## Instalación en Nuevo Computador

### Requisitos Previos
- Docker y Docker Compose instalados
- Git instalado

### Instalación Automática (4 comandos)

```bash
# 1. Clonar el repositorio
git clone [URL_DEL_REPOSITORIO]
cd Simulaci-n_Curricular

# 2. Dar permisos al script principal
chmod +x ./docker.sh

# 3. Ejecutar setup completo (¡esto hace todo!)
./docker.sh setup

# 4. ¡Listo! La aplicación está corriendo en:
# http://localhost:8080
```

### ¿Qué hace `./docker.sh setup`?

El comando `setup` ejecuta automáticamente:

1. ✅ **Permisos**: Configura permisos de ejecución para todos los scripts
2. ✅ **Entorno**: Crea `.env` desde `.env.example` si no existe
3. ✅ **Docker**: Construye y inicia todos los contenedores
4. ✅ **Base de Datos**: Ejecuta migraciones, seeders de materias y prerrequisitos
5. ✅ **Datos**: Importa estudiantes reales desde CSV (si está disponible)

### Comandos Adicionales

```bash
# Ver todos los comandos disponibles
./docker.sh help

# Configurar solo permisos (si hay problemas)
./docker.sh permissions

# Ver logs de la aplicación
./docker.sh logs app

# Verificar estado de la base de datos
./docker.sh db-status

# Acceder al contenedor de la aplicación
./docker.sh shell

# Ejecutar comandos de Laravel
./docker.sh artisan migrate:status
./docker.sh artisan import:academic-history datasets/archivo.csv
```

### Estructura del Proyecto

```
📁 Simulación Curricular/
├── 🐳 docker.sh              # Script principal de gestión
├── 🐳 docker-compose.yml     # Configuración de contenedores
├── 📁 datasets/              # Archivos CSV (ignorados por git)
├── 📁 docker/
│   ├── init_db.sh           # Inicialización de base de datos
│   └── php/entrypoint.sh    # Configuración de PHP
└── 📁 app/
    ├── Console/Commands/    # Comandos de importación
    └── Services/           # Lógica de negocio
```

### Seguridad de Datos

- 🔒 Los archivos CSV con datos reales están **excluidos del control de versiones**
- 🔒 La información confidencial permanece solo en tu computador local
- 🔒 El sistema genera nombres aleatorios para proteger la privacidad

### Solución de Problemas

**Error de permisos:**
```bash
./docker.sh permissions
```

**Error en la base de datos:**
```bash
./docker.sh artisan migrate:fresh --seed
```

**Reiniciar todo:**
```bash
docker-compose down -v
./docker.sh setup
```

---

🎯 **¡Con solo 4 comandos tienes todo funcionando!**
