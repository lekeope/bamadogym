#!/bin/sh
set -e

DB_HOST="${DB_HOST:-}"
DB_PORT="${DB_PORT:-5432}"

if [ -n "$DB_HOST" ] && [ "${SKIP_DB_WAIT:-false}" != "true" ]; then
    echo "Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT}..."

    until php -r "
try {
    new PDO(
        sprintf('pgsql:host=%s;port=%s;dbname=%s', getenv('DB_HOST'), getenv('DB_PORT') ?: '5432', getenv('DB_DATABASE')),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    exit(0);
} catch (Throwable \$e) {
    exit(1);
}
" >/dev/null 2>&1; do
        sleep 1
    done

    echo "PostgreSQL is ready."
fi

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ] && [ -n "$DB_HOST" ]; then
    echo "Running migrations..."
    php artisan migrate --force
fi

if [ "${RUN_SEEDERS:-false}" = "true" ] && [ -n "$DB_HOST" ]; then
    echo "Running seeders..."
    php artisan db:seed --force
fi

if [ "${APP_ENV}" = "production" ] || [ "${CACHE_CONFIG:-true}" = "true" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan event:cache || true
fi

php artisan storage:link --force >/dev/null 2>&1 || true

exec "$@"
