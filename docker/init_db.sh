#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[DB-INIT]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[DB-INIT]${NC} $1"
}

print_error() {
    echo -e "${RED}[DB-INIT]${NC} $1"
}

# Wait for database to be ready
print_status "Waiting for database to be ready..."
until docker-compose exec -T db pg_isready -U ${DB_USERNAME} -d ${DB_DATABASE} > /dev/null 2>&1; do
    print_warning "Database is not ready yet, waiting..."
    sleep 2
done

print_status "Database is ready!"

# Run migrations
print_status "Running database migrations..."
docker-compose exec -T app php artisan migrate:fresh

# Check if we should seed the database
if [ "$1" = "--seed" ]; then
    print_status "Seeding database with basic data..."
    # Seed subjects and prerequisites - essential for the system
    docker-compose exec -T app php artisan db:seed --class=SubjectSeeder --force
    docker-compose exec -T app php artisan db:seed --class=PrerequisitesSeeder --force
    
    # Import students from CSV if file exists
    CSV_FILE="/app/datasets/Admon SI - Estudaintes activos con sus asignaturas - RE_MIG_PLA_EST_DATOS.csv"
    print_status "Checking if CSV file exists in container..."
    
    # Check if file exists inside the container
    if docker-compose exec -T app test -f "$CSV_FILE"; then
        print_status "CSV file found! Importing student academic history..."
        docker-compose exec -T app php artisan import:academic-history "$CSV_FILE"
        
        # Update student progress percentages
        print_status "Updating student progress percentages..."
        docker-compose exec -T app php artisan tinker --execute="echo 'Updated ' . \App\Models\Student::recalculateAllProgress() . ' students progress';"
    else
        print_warning "CSV file not found at $CSV_FILE inside container. Skipping student import."
        print_warning "Available files in datasets directory:"
        docker-compose exec -T app ls -la /app/datasets/ || print_warning "datasets directory does not exist"
        print_warning "You can manually import students later with:"
        print_warning "  ./docker.sh artisan import:academic-history \"/app/datasets/your-file.csv\""
    fi
fi

# Create subjects table data if it doesn't exist
print_status "Checking if subjects table has data..."
SUBJECT_COUNT=$(docker-compose exec -T app php artisan tinker --execute="echo \App\Models\Subject::count();" | tail -1)

if [ "$SUBJECT_COUNT" -eq 0 ]; then
    print_status "Subjects table is empty, running subject seeder..."
    docker-compose exec -T app php artisan db:seed --class=SubjectSeeder --force
else
    print_status "Subjects table already has $SUBJECT_COUNT records."
fi

# Create prerequisites table data if it doesn't exist
print_status "Checking if prerequisites table has data..."
PREREQ_COUNT=$(docker-compose exec -T app php artisan tinker --execute="echo DB::table('subject_prerequisites')->count();" | tail -1)

if [ "$PREREQ_COUNT" -eq 0 ]; then
    print_status "Prerequisites table is empty, running prerequisites seeder..."
    docker-compose exec -T app php artisan db:seed --class=PrerequisitesSeeder --force
else
    print_status "Prerequisites table already has $PREREQ_COUNT records."
fi

print_status "Database initialization complete!"
