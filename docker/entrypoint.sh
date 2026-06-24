#!/bin/sh
set -e

if [ "$1" = "supervisord" ]; then
    rm -f /var/www/html/storage/installed

    # Ensure storage directories exist with correct permissions
    mkdir -p storage/app/private storage/app/public \
        storage/framework/cache/data storage/framework/sessions \
        storage/framework/views storage/logs
    chown -R www-data:www-data storage bootstrap/cache public
    chmod -R 775 storage bootstrap/cache public

    echo "Waiting for MySQL..."

    retries=30
    until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null || [ $retries -le 0 ]; do
        sleep 2
        retries=$((retries - 1))
    done

    if [ $retries -gt 0 ]; then
        echo "MySQL is ready. Running migrations..."
        php artisan migrate --force 2>&1 || echo "WARNING: Migration failed (non-fatal)"
        php artisan optimize 2>&1 || echo "WARNING: Optimize failed (non-fatal)"
        echo "Application ready."
    else
        echo "WARNING: MySQL did not become ready in time. Starting anyway..."
    fi
fi

exec "$@"
