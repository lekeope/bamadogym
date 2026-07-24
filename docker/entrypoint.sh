#!/bin/sh
set -e

PORT="${PORT:-80}"
# libpq: fail fast on migrate/connect instead of hanging
export PGCONNECT_TIMEOUT="${DB_CONNECT_TIMEOUT:-3}"

# Coolify / platforms set PORT — nginx must listen on the same port.
sed "s/LISTEN_PORT/${PORT}/g" /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf
echo "Nginx will listen on port ${PORT}"

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

# Do not wait for Postgres. Try migrate once; if DB is down, start the web UI anyway.
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Attempting migrations (non-blocking)..."
    php artisan migrate --force --no-interaction \
        || echo "WARNING: migrate skipped/failed — app will still start. Fix DB_* env if the UI reports a database error."
fi

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "Attempting seeders (non-blocking)..."
    php artisan db:seed --force --no-interaction \
        || echo "WARNING: seed skipped/failed."
fi

if [ "${APP_ENV}" = "production" ] || [ "${CACHE_CONFIG:-true}" = "true" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan event:cache || true
fi

php artisan storage:link --force >/dev/null 2>&1 || true

exec "$@"
