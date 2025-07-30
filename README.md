# Docker Setup for Simulación Curricular

This document explains how to set up and run the Simulación Curricular project using Docker with separated services.

## Prerequisites

- Docker
- Docker Compose
- Git

## Architecture

The project uses a multi-container setup:
- **app**: PHP 8.3-FPM with auto-initialization
- **web**: Nginx web server
- **db**: PostgreSQL 15 database

## Quick Start

1. **Clone the repository** (if not already done):
   ```bash
   git clone <repository-url>
   cd Simulaci-n_Curricular
   ```

2. **Set up environment variables**:
   ```bash
   # Copy and edit the environment file
   cp .env.example .env
   
   # Edit the .env file with your database credentials
   nano .env
   ```

3. **Start the application**:
   ```bash
   # Make the script executable
   chmod +x docker.sh
   
   # Run the initial setup
   ./docker.sh setup
   ```

   This will:
   - Build the Docker containers
   - Install PHP dependencies automatically
   - Generate application key
   - Run database migrations
   - Seed the database with initial data

4. **Access the application**:
   - Application: http://localhost:8080
   - Database: localhost:5432

## Docker Services

### App Container
- **Image**: Custom PHP 8.3-FPM
- **Features**: 
  - Auto-installs Composer dependencies
  - Auto-generates Laravel key
  - Installs Laravel Breeze for authentication
  - Installs Pest for testing
  - Builds NPM assets automatically

### Web Container
- **Image**: Nginx Alpine
- **Port**: 8080
- **Function**: Serves the Laravel application

### Database Container
- **Image**: PostgreSQL 15
- **Port**: 5432
- **Persistence**: Data stored in Docker volume

## Available Commands

The `docker.sh` script provides convenient commands:

```bash
# Initial setup
./docker.sh setup

# Start containers
./docker.sh start

# Stop containers
./docker.sh stop

# Restart containers
./docker.sh restart

# View logs (all services or specific service)
./docker.sh logs
./docker.sh logs app

# Access app container shell
./docker.sh shell

# Access database shell
./docker.sh db-shell

# Run Laravel artisan commands
./docker.sh artisan migrate
./docker.sh artisan db:seed

# Run composer commands
./docker.sh composer install
./docker.sh composer update

# Run npm commands
./docker.sh npm install
./docker.sh npm run dev

# Show help
./docker.sh help
```

## Environment Variables

### Required Environment Variables

Create a `.env` file based on `.env.example` and set:

```env
# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=simulacion_curricular
DB_USERNAME=postgres
DB_PASSWORD=your_secure_password_here

# Application
APP_KEY=your_app_key_here
APP_URL=http://localhost:8080
```

## Auto-Initialization Features

The app container includes an entrypoint script that automatically:

1. **Installs Dependencies**: Runs `composer install` if vendor directory is missing
2. **Environment Setup**: Copies `.env.example` to `.env` if missing
3. **Key Generation**: Generates Laravel application key
4. **Laravel Breeze**: Installs authentication scaffolding
5. **Pest Testing**: Installs Pest testing framework
6. **Asset Building**: Runs `npm install` and `npm run build`
7. **Database Migrations**: Runs migrations when database is ready
8. **Permissions**: Sets proper file permissions

## Database Initialization

The `docker/init_db.sh` script provides additional database setup:

```bash
# Initialize database with seeding
./docker/init_db.sh --seed

# Check database status
./docker/init_db.sh
```

## Development Workflow

1. **Start development environment**:
   ```bash
   ./docker.sh start
   ```

2. **Make changes** to your code (files are automatically synced)

3. **Run migrations** when needed:
   ```bash
   ./docker.sh artisan migrate
   ```

4. **Install Excel package for convalidations** (if not auto-installed):
   ```bash
   ./docker.sh composer require maatwebsite/excel
   ```

5. **Access the convalidation system**:
   ```bash
   # Navigate to http://localhost:8080/convalidation
   # Or click "Realizar Convalidación" in the main simulation view
   ```

4. **Run tests**:
   ```bash
   ./docker.sh artisan test
   ```

5. **View logs**:
   ```bash
   ./docker.sh logs app
   ./docker.sh logs web
   ./docker.sh logs db
   ```

## Sistema de Convalidaciones

El proyecto incluye un sistema completo de convalidaciones curriculares que permite:

### Características
- **Carga de mallas externas**: Importación desde CSV (.csv)
- **Convalidación manual**: Equivalencias directas y libre elección
- **Sugerencias automáticas**: Basadas en similitud de nombres
- **Dashboard estadístico**: Progreso y métricas en tiempo real
- **Reportes exportables**: Documentación completa del proceso

### Configuración Inicial
```bash
# Ejecutar migraciones específicas de convalidaciones
./docker.sh artisan migrate

# Verificar que las tablas se crearon correctamente
./docker.sh artisan tinker
# En tinker: \App\Models\ExternalCurriculum::count()
```

### Uso del Sistema
1. **Acceder**: http://localhost:8080/convalidation
2. **Cargar malla**: Botón "Realizar Convalidación"
3. **Formato CSV**: Ver detalles del formato más abajo
4. **Convalidar**: Configurar cada materia como directa o libre elección
5. **Seguimiento**: Ver progreso en tiempo real sin recargas de página
6. **Exportar**: Generar reporte final de convalidaciones

### Características Avanzadas
- **Progreso de Carrera**: Cálculo automático del porcentaje de carrera completada basado en créditos
- **Navegación Inteligente**: Mantiene la posición en el semestre actual durante convalidaciones
- **Estadísticas en Tiempo Real**: Actualización automática sin recargar la página
- **Plantilla CSV**: Descarga automática de formato de ejemplo

### Formato de Archivo CSV para Mallas Externas

**Campos Obligatorios:**
- `codigo` - Código único de la materia (ej: "INF101")
- `nombre` - Nombre completo de la materia (ej: "Programación I")

**Campos Opcionales:**
- `creditos` - Número de créditos (ej: 3, 4, 5)
- `semestre` - Semestre de la materia (ej: 1, 2, 3)
- `descripcion` - Descripción de la materia

**Ejemplo de CSV:**
```csv
codigo,nombre,creditos,semestre,descripcion
INF101,Introducción a la Informática,3,1,Conceptos básicos
MAT101,Matemáticas I,4,1,Álgebra y cálculo básico
PRG101,Programación I,4,2,Fundamentos de programación
```

**Requisitos técnicos:**
- Formato: CSV (separado por comas)
- Tamaño máximo: 10MB
- Codificación: UTF-8 recomendada
- Primera fila debe contener los nombres de columnas

### Porcentaje de Equivalencia
El sistema permite asignar un **porcentaje de equivalencia** (0-100%) a cada convalidación:

- **100%**: Equivalencia total (contenido idéntico)
- **80-99%**: Equivalencia alta (contenido muy similar)
- **60-79%**: Equivalencia parcial (contenido parcialmente cubierto)
- **30-59%**: Equivalencia mínima (elementos básicos cubiertos)

**Ejemplos:**
```
- "Programación I" externa → "Programación I" interna: 100%
- "Fundamentos de Programación" → "Programación I": 85%
- "Introducción a Algoritmos" → "Programación I": 60%
```

El porcentaje afecta el cálculo de créditos convalidados para el progreso de carrera.

### Troubleshooting Convalidaciones
```bash
# Si hay errores de tablas faltantes
./docker.sh artisan migrate:status
./docker.sh artisan migrate

# Para reset completo del sistema de convalidaciones
./docker.sh artisan migrate:rollback --path=database/migrations/2025_07_26_000001_create_external_curriculums_table.php
./docker.sh artisan migrate
```

## Troubleshooting

### Container Issues

```bash
# Check container status
docker-compose ps

# View specific container logs
./docker.sh logs app
./docker.sh logs web
./docker.sh logs db

# Restart specific service
docker-compose restart app
```

### Database Issues

```bash
# Access database directly
./docker.sh db-shell

# Reset database
./docker.sh artisan migrate:fresh --seed
```

### Permission Issues

```bash
# Fix Laravel permissions (handled automatically by entrypoint)
./docker.sh shell
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Build Issues

```bash
# Rebuild containers
docker-compose down
docker-compose up -d --build

# Clear Docker cache
docker system prune -f
```

## File Structure

```
docker/
├── nginx/
│   └── default.conf    # Nginx configuration
├── php/
│   ├── Dockerfile      # PHP container definition
│   ├── entrypoint.sh   # Auto-initialization script
│   └── local.ini       # PHP configuration
└── init_db.sh          # Database initialization script
```

## Ports

- **8080**: Nginx web server
- **5432**: PostgreSQL database
- **9000**: PHP-FPM (internal)

## Security Notes

- Environment files with real passwords should not be committed
- Use strong passwords for production environments
- The app container runs as www-data user for security
- Database is isolated in its own container