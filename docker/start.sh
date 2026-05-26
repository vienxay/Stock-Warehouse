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

# Ensure required storage directories exist
mkdir -p storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache/data \
         storage/logs \
         storage/app/public

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Artisan requires .env to exist even when all values come from environment variables
[ -f .env ] || touch .env

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Cache config/routes/views for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache on port ${APP_PORT}..."
exec apache2-foreground
