#!/bin/sh
set -e

PORT="${PORT:-80}"

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

# webdevops PHP-FPM runs as `application` (UID 1000), not www-data.
chown -R application:application storage bootstrap/cache || true

# Marketing MVP — no database migrations.
if [ "${APP_ENV}" = "production" ] || [ "${CACHE_CONFIG:-true}" = "true" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan event:cache || true
fi

php artisan storage:link --force >/dev/null 2>&1 || true

exec "$@"
