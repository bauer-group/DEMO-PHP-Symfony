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

# Sync dependencies (without auto-scripts to control importmap:install separately)
echo "==> Syncing Composer dependencies..."
su-exec app composer install --no-interaction --prefer-dist --no-scripts
su-exec app composer dump-autoload

# Run Symfony auto-scripts manually (except importmap:install)
echo "==> Installing assets..."
timeout 60 su-exec app php bin/console assets:install public || echo "==> Warning: assets:install failed (exit code: $?), continuing..."

# Install importmap vendor assets (Turbo, Stimulus) with timeout
echo "==> Installing importmap assets..."
timeout 60 su-exec app php bin/console importmap:install || echo "==> Warning: importmap:install failed (exit code: $?), continuing..."

# Clear old cache to ensure fresh config is used
echo "==> Clearing cache..."
rm -rf var/cache/*

# Wait for database to be ready
echo "==> Waiting for database connection..."
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if su-exec app php bin/console dbal:run-sql "SELECT 1" 2>&1; then
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
