#!/bin/sh
# =============================================================================
# Development Entrypoint Script
# =============================================================================

set -e

echo "==> Starting PHP Todo Application (Development Mode)..."

cd /app

# Install dependencies if vendor directory is empty
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "==> Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist
fi

# Wait for database to be ready
echo "==> Waiting for database connection..."
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; then
        echo "==> Database is ready!"
        break
    fi
    attempt=$((attempt + 1))
    echo "==> Waiting for database... ($attempt/$max_attempts)"
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "==> ERROR: Could not connect to database after $max_attempts attempts"
    exit 1
fi

# Run database migrations
echo "==> Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Clear cache
echo "==> Clearing cache..."
php bin/console cache:clear

# Create var directories
mkdir -p var/cache var/log

# Start PHP built-in server (for development)
echo "==> Starting PHP development server on port 80..."
exec php -S 0.0.0.0:80 -t public
