# Docker Setup for Simulación Curricular

This document explains how to set up and run the Simulación Curricular project using Docker.

## Prerequisites

- Docker
- Docker Compose
- Git

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
   # You can use .env.local as a reference
   ```

3. **Start the application**:
   ```bash
   # Make the script executable (if not already done)
   chmod +x docker.sh
   
   # Run the initial setup
   ./docker.sh setup
   ```

   This will:
   - Build the Docker containers
   - Install PHP dependencies
   - Generate application key
   - Run database migrations
   - Seed the database with initial data

4. **Access the application**:
   - Application: http://localhost
   - Database: localhost:5432

## Docker Services

The docker-compose.yml file includes:

- **app**: Laravel application with PHP 8.3, Nginx, and Supervisor
- **postgres**: PostgreSQL 15 database
- **redis**: Redis cache (optional)

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

# View logs
./docker.sh logs

# Access app container shell
./docker.sh shell

# Run Laravel artisan commands
./docker.sh artisan migrate
./docker.sh artisan db:seed

# Run composer commands
./docker.sh composer install
./docker.sh composer update

# Show help
./docker.sh help
```

## Environment Variables

### Required Environment Variables

Create a `.env` file based on `.env.example` and set:

```env
# Database Configuration (use these in your .env file)
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=simulacion_curricular
DB_USERNAME=postgres
DB_PASSWORD=your_secure_password_here

# Application
APP_KEY=your_app_key_here
APP_URL=http://localhost
```

### Docker Compose Environment Variables

Create a `.env.local` file for Docker Compose variables:

```env
# Database credentials for Docker containers
POSTGRES_DB=simulacion_curricular
POSTGRES_USER=postgres
POSTGRES_PASSWORD=your_secure_password_here
```

## Directory Structure

```
docker/
├── nginx/
│   ├── default.conf    # Nginx virtual host configuration
│   └── nginx.conf      # Main Nginx configuration
├── php/
│   └── local.ini       # PHP configuration
└── supervisor/
    └── supervisord.conf # Supervisor configuration for managing processes
```

## Security Notes

- **Never commit passwords**: Environment files with real passwords should not be committed
- **Use strong passwords**: Generate secure passwords for production environments
- **Environment separation**: Use different passwords for development and production

## Troubleshooting

### Docker Buildx Warning

If you see the message "Docker Compose is configured to build using Bake, but buildx isn't installed", you have several options:

**Option 1: Install Docker Buildx (Recommended)**
```bash
# For Arch/CachyOS
sudo pacman -S docker-buildx

# For Ubuntu/Debian
sudo apt-get install docker-buildx-plugin

# For other systems, check Docker documentation
```

**Option 2: Use the no-buildx compose file**
```bash
# Use the alternative compose file
docker-compose -f docker-compose.no-buildx.yml up -d
```

**Option 3: Set Docker to use legacy builder**
```bash
# Disable buildx globally
docker buildx use default
```

### Container Issues

```bash
# Check container status
docker-compose ps

# View container logs
docker-compose logs app
docker-compose logs postgres

# Restart specific service
docker-compose restart app
```

### Database Issues

```bash
# Access database container
docker-compose exec postgres psql -U postgres -d simulacion_curricular

# Reset database
./docker.sh artisan migrate:fresh --seed
```

### Permission Issues

```bash
# Fix Laravel permissions
./docker.sh shell
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
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
   ./docker.sh logs
   ```

## Production Considerations

- Use environment-specific `.env` files
- Implement proper secrets management
- Configure SSL/TLS certificates
- Set up proper logging and monitoring
- Use production-optimized Docker images
- Configure proper backup strategies

## Ports

- **80**: Nginx web server
- **5432**: PostgreSQL database
- **6379**: Redis cache
