#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check Docker installation
check_docker() {
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed. Please install Docker first."
        exit 1
    fi
    
    if ! docker info &> /dev/null; then
        print_error "Docker daemon is not running. Please start Docker service."
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
        exit 1
    fi
}

# Check Docker Buildx
check_buildx() {
    if ! docker buildx version &>/dev/null; then
        print_warning "Docker Buildx not found. Installing..."
        # For Arch-based systems
        if command -v pacman &> /dev/null; then
            print_status "Installing docker-buildx via pacman..."
            sudo pacman -S docker-buildx --noconfirm
        else
            print_warning "Please install docker-buildx manually for your system"
        fi
    fi
}

# Check if .env file exists
check_env_file() {
    if [ ! -f .env ]; then
        print_warning ".env file not found. Please create one based on .env.example"
        print_status "You can use .env.local as a reference"
        return 1
    fi
    return 0
}

# Setup the project
setup() {
    print_status "Setting up Simulación Curricular project..."
    
    # Check Docker installation
    check_docker
    
    # Check for Buildx
    check_buildx
    
    # Check for .env file
    if ! check_env_file; then
        print_error "Please create .env file first"
        exit 1
    fi
    
    # Build and start containers
    print_status "Building and starting containers..."
    docker-compose up -d --build
    
    # Wait for database to be ready
    print_status "Waiting for database to be ready..."
    sleep 10
    
    # Install dependencies
    print_status "Installing PHP dependencies..."
    docker-compose exec app composer install
    
    # Generate application key
    print_status "Generating application key..."
    docker-compose exec app php artisan key:generate
    
    # Run migrations
    print_status "Running migrations..."
    docker-compose exec app php artisan migrate
    
    # Seed database
    print_status "Seeding database..."
    docker-compose exec app php artisan db:seed
    
    print_status "Setup complete! Application is running at http://localhost"
}

# Start the application
start() {
    print_status "Starting containers..."
    docker-compose up -d
}

# Stop the application
stop() {
    print_status "Stopping containers..."
    docker-compose down
}

# Restart the application
restart() {
    print_status "Restarting containers..."
    docker-compose down
    docker-compose up -d
}

# Show logs
logs() {
    docker-compose logs -f
}

# Access application container
shell() {
    docker-compose exec app bash
}

# Run artisan commands
artisan() {
    docker-compose exec app php artisan "$@"
}

# Run composer commands
composer() {
    docker-compose exec app composer "$@"
}

# Show help
help() {
    echo "Simulación Curricular - Docker Management Script"
    echo ""
    echo "Usage: ./docker.sh [command]"
    echo ""
    echo "Commands:"
    echo "  setup     - Initial setup (build, migrate, seed)"
    echo "  start     - Start containers"
    echo "  stop      - Stop containers"
    echo "  restart   - Restart containers"
    echo "  logs      - Show container logs"
    echo "  shell     - Access application container"
    echo "  artisan   - Run Laravel artisan commands"
    echo "  composer  - Run composer commands"
    echo "  help      - Show this help message"
    echo ""
    echo "Examples:"
    echo "  ./docker.sh setup"
    echo "  ./docker.sh artisan migrate"
    echo "  ./docker.sh composer install"
}

# Main script logic
case "$1" in
    setup)
        setup
        ;;
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        restart
        ;;
    logs)
        logs
        ;;
    shell)
        shell
        ;;
    artisan)
        shift
        artisan "$@"
        ;;
    composer)
        shift
        composer "$@"
        ;;
    help|*)
        help
        ;;
esac
