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

# Write all env vars explicitly to .env so Laravel config:cache reads them correctly
cat > .env << EOF
APP_NAME="${APP_NAME:-Stock Management}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
APP_LOCALE=${APP_LOCALE:-lo}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-file}
CACHE_STORE=${CACHE_STORE:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-public}

LOG_CHANNEL=${LOG_CHANNEL:-stderr}
LOG_LEVEL=${LOG_LEVEL:-error}
EOF

# Ensure required storage directories exist
mkdir -p storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache/data \
         storage/logs \
         storage/app/public

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# Run migrations safely (ไม่ใช้ migrate:fresh เพราะจะลบข้อมูลทั้งหมด!)
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Seed default users (firstOrCreate = safe to run multiple times)
echo "Seeding default users..."
php artisan db:seed --class=RolesAndPermissionsSeeder --force --no-interaction
php artisan db:seed --class=AdminUserSeeder --force --no-interaction

# Cache config/routes/views for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache on port ${APP_PORT}..."
exec apache2-foreground