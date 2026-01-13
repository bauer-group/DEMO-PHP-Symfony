#!/bin/sh
# =============================================================================
# Development Entrypoint Script
# =============================================================================
# Runs as root initially (like production), handles permissions,
# then switches to app user for the PHP server.

set -e

echo "==> Starting PHP Todo Application (Development Mode)..."

cd /app

# Fix permissions for mounted volumes
echo "==> Fixing permissions..."
mkdir -p var/cache var/log vendor
chown -R app:app var vendor

# Install dependencies if vendor directory is empty (as app user)
if [ ! -f "vendor/autoload.php" ]; then
    echo "==> Installing Composer dependencies..."
    su-exec app composer install --no-interaction --prefer-dist
fi

# Wait for database to be ready
echo "==> Waiting for database connection..."
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if su-exec app php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; then
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
su-exec app php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Clear cache
echo "==> Clearing cache..."
su-exec app php bin/console cache:clear

# Start PHP built-in server as app user (for development)
echo "==> Starting PHP development server on port 80..."
exec su-exec app php -S 0.0.0.0:80 -t public
