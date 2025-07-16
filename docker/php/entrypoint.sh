#!/bin/bash
set -e

# Wait a moment for volume to be mounted
sleep 2

# Check if vendor directory exists, if not install dependencies
if [ ! -d "/app/vendor" ]; then
    echo "Installing Composer dependencies..."
    cd /app
    composer install --optimize-autoloader
fi

# Check if .env exists, if not copy from example
if [ ! -f "/app/.env" ]; then
    echo "Creating .env file..."
    cp /app/.env.example /app/.env
fi

# Generate application key if not exists
if ! grep -q "APP_KEY=base64:" /app/.env; then
    echo "Generating application key..."
    php artisan key:generate --no-interaction
fi

# Set cache driver to file to avoid database cache issues
if ! grep -q "CACHE_STORE=" /app/.env; then
    echo "Setting cache driver to file..."
    echo "CACHE_STORE=file" >> /app/.env
else
    # Make sure cache is set to file during initialization
    sed -i 's/CACHE_STORE=.*/CACHE_STORE=file/' /app/.env
fi

# Install Pest if not already installed
if [ ! -f "/app/composer.lock" ] || ! grep -q "pestphp/pest" /app/composer.lock; then
    echo "Installing Pest..."
    composer require pestphp/pest --dev --no-interaction
    
    # Create Pest.php if it doesn't exist
    if [ ! -f "/app/tests/Pest.php" ]; then
        echo "Creating Pest configuration..."
        touch /app/tests/Pest.php
        echo "<?php declare(strict_types=1);" > /app/tests/Pest.php
    fi
fi

# Install npm dependencies and build assets if package.json exists
if [ -f "/app/package.json" ]; then
    if [ ! -d "/app/node_modules" ]; then
        echo "Installing npm dependencies..."
        cd /app
        npm install
    fi
    
    # Check if assets need to be built
    if [ ! -d "/app/public/build" ]; then
        echo "Building assets..."
        npm run build
    fi
fi

# Set proper permissions
chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Run database migrations if needed
echo "Checking database migrations..."
# Wait for database to be ready
max_attempts=30
attempt=0
while [ $attempt -lt $max_attempts ]; do
    if php artisan migrate:status --no-interaction 2>/dev/null; then
        echo "Database is ready. Running migrations..."
        php artisan migrate --force --no-interaction
        break
    else
        echo "Database not ready yet, waiting... (attempt $((attempt+1))/$max_attempts)"
        sleep 2
        attempt=$((attempt+1))
    fi
done

if [ $attempt -eq $max_attempts ]; then
    echo "Warning: Database not ready after $max_attempts attempts. Continuing without migrations."
fi

# Clear cache to ensure configuration is loaded
echo "Clearing application cache..."
php artisan config:clear --no-interaction

# Only clear cache if database is ready
if php artisan migrate:status --no-interaction 2>/dev/null | grep -q "Migration table created successfully\|Migration table found"; then
    echo "Clearing database cache..."
    php artisan cache:clear --no-interaction
else
    echo "Database not ready, skipping database cache clear..."
fi

echo "Initialization complete. Starting PHP-FPM..."

# Execute the original command
exec "$@"