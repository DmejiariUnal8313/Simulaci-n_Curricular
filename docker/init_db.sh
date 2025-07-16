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
docker-compose exec -T app php artisan migrate --force

# Check if we should seed the database
if [ "$1" = "--seed" ]; then
    print_status "Seeding database..."
    docker-compose exec -T app php artisan db:seed --force
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

print_status "Database initialization complete!"
