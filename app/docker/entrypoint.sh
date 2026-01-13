#!/bin/sh
# =============================================================================
# Production Entrypoint Script
# =============================================================================

set -e

echo "==> Starting PHP Todo Application..."

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

# Clear and warm up cache
echo "==> Warming up cache..."
php bin/console cache:clear --no-warmup
php bin/console cache:warmup

# Fix permissions
echo "==> Fixing permissions..."
chown -R app:app /app/var

echo "==> Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
