#!/bin/sh
set -e

if [ "$1" = "supervisord" ]; then
    rm -f /var/www/html/storage/installed

    # Ensure storage directories exist with correct permissions
    mkdir -p storage/app/private storage/app/public \
        storage/framework/cache/data storage/framework/sessions \
        storage/framework/views storage/logs

    # Create .env if it doesn't exist (needed for install wizard permissions check)
    [ -f .env ] || touch .env

    chown -R www-data:www-data .env storage bootstrap/cache public
    chmod -R 775 .env storage bootstrap/cache public

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

        # Ensure device_user pivot table has timestamp columns
        php -r "
            \$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');
            \$pdo->exec('USE ${DB_DATABASE}');
            \$stmt = \$pdo->query('SHOW COLUMNS FROM device_user LIKE \"created_at\"');
            if (!\$stmt->fetch()) {
                \$pdo->exec('ALTER TABLE device_user ADD COLUMN created_at TIMESTAMP NULL, ADD COLUMN updated_at TIMESTAMP NULL');
                echo \"Added timestamp columns to device_user.\n\";
            }
        " 2>&1 || echo "WARNING: Could not check/add device_user columns"

        # Ensure full write access for cache subdirectory creation
        chmod -R 777 storage/framework/cache
        php artisan cache:clear 2>&1 || true

        echo "Application ready."
    else
        echo "WARNING: MySQL did not become ready in time. Starting anyway..."
    fi
fi

exec "$@"
