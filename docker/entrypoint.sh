#!/bin/sh
set -e

PORT="${PORT:-80}"
DB_HOST="${DB_HOST:-}"
DB_PORT="${DB_PORT:-5432}"
DB_WAIT_TIMEOUT="${DB_WAIT_TIMEOUT:-60}"

# Coolify / platforms set PORT — nginx must listen on the same port.
sed "s/LISTEN_PORT/${PORT}/g" /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf
echo "Nginx will listen on port ${PORT}"

if [ -n "$DB_HOST" ] && [ "${SKIP_DB_WAIT:-false}" != "true" ]; then
    echo "Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT} (timeout ${DB_WAIT_TIMEOUT}s)..."
    i=0
    until php -r "
try {
    new PDO(
        sprintf('pgsql:host=%s;port=%s;dbname=%s', getenv('DB_HOST'), getenv('DB_PORT') ?: '5432', getenv('DB_DATABASE')),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    exit(0);
} catch (Throwable \$e) {
    fwrite(STDERR, \$e->getMessage() . PHP_EOL);
    exit(1);
}
" 2>/tmp/db-wait.err; do
        i=$((i + 1))
        if [ "$i" -ge "$DB_WAIT_TIMEOUT" ]; then
            echo "WARNING: PostgreSQL not reachable after ${DB_WAIT_TIMEOUT}s — starting web anyway."
            cat /tmp/db-wait.err 2>/dev/null || true
            break
        fi
        sleep 1
    done

    if [ "$i" -lt "$DB_WAIT_TIMEOUT" ]; then
        echo "PostgreSQL is ready."
    fi
fi

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache \
    /var/log/nginx \
    /var/lib/nginx/body \
    /run/nginx

chown -R www-data:www-data storage bootstrap/cache || true

# Never block container start on migrate/seed failures (avoids Coolify Bad Gateway).
if [ "${RUN_MIGRATIONS:-true}" = "true" ] && [ -n "$DB_HOST" ]; then
    echo "Running migrations..."
    php artisan migrate --force || echo "WARNING: migrate failed — check DB credentials."
fi

if [ "${RUN_SEEDERS:-false}" = "true" ] && [ -n "$DB_HOST" ]; then
    echo "Running seeders..."
    php artisan db:seed --force || echo "WARNING: seed failed."
fi

if [ "${APP_ENV}" = "production" ] || [ "${CACHE_CONFIG:-true}" = "true" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan event:cache || true
fi

php artisan storage:link --force >/dev/null 2>&1 || true

exec "$@"
