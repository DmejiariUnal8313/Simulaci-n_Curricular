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

## Production Considerations

- Use environment-specific `.env` files
- Implement proper secrets management
- Configure SSL/TLS certificates
- Set up proper logging and monitoring
- Use production-optimized Docker images
- Configure proper backup strategies
- Consider using Docker Swarm or Kubernetes for orchestration
