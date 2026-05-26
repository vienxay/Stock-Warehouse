#!/bin/bash
set -e

cd /var/www/html

# Render injects $PORT; configure Apache to listen on it
APP_PORT="${PORT:-80}"

if [ "$APP_PORT" != "80" ]; then
    sed -i "s/Listen 80/Listen ${APP_PORT}/" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${APP_PORT}>/" \
        /etc/apache2/sites-available/000-default.conf
fi

# Generate app key if not provided
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Create storage symlink (ignore if already exists)
php artisan storage:link --force 2>/dev/null || true

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Cache for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache on port ${APP_PORT}..."
exec apache2-foreground
