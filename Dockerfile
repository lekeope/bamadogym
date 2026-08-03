# ---- Frontend assets ----
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# ---- PHP dependencies ----
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev --no-interaction

# ---- Runtime: nginx + PHP-FPM ----
# webdevops ships intl/pdo_pgsql/bcmath/opcache/pcntl/zip already compiled.
# Official php:* + install-php-extensions compiles those on Coolify and OOMs small VPSes.
FROM webdevops/php:8.3

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV PORT=80
ENV WEB_DOCUMENT_ROOT=/var/www/html/public
# ionCube installs opcode handlers and forces PHP to disable JIT (noisy warnings).
ENV PHP_DISMOD=ioncube

USER root

# nginx for our reverse proxy; supervisor is already in the base image.
# Strip webdevops supervisor programs so only our supervisord.conf runs.
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends nginx; \
    rm -rf /var/lib/apt/lists/*; \
    rm -f /etc/nginx/sites-enabled/default; \
    mkdir -p /etc/nginx/templates /etc/nginx/conf.d /var/log/nginx /var/lib/nginx/body /run/nginx /var/log/supervisor; \
    rm -f /opt/docker/etc/supervisor.d/*.conf /etc/supervisor/conf.d/*.conf || true; \
    # Drop ionCube so JIT warning cannot fire even without their entrypoint.
    find /usr/local/etc/php /opt/docker/etc/php -type f \( -iname '*ioncube*' -o -iname '*ion_cube*' \) -delete 2>/dev/null || true; \
    # Avoid two pools binding :9000 (official www + webdevops application).
    rm -f /usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf.default || true

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-bamado.ini
COPY docker/php/php.ini /opt/docker/etc/php/php.ini
COPY docker/php/opcache.ini /opt/docker/etc/php/opcache-bamado.ini
# Replace pool config (do not stack a second [application] / [www] on :9000)
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY docker/php/zz-docker.conf /opt/docker/etc/php/fpm/pool.d/application.conf
COPY docker/nginx/default.conf /etc/nginx/templates/default.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# webdevops PHP-FPM runs as `application` (UID 1000)
COPY --chown=application:application --from=vendor /app/vendor ./vendor
COPY --chown=application:application . .
COPY --chown=application:application --from=assets /app/public/build ./public/build

RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        storage/app/public \
        bootstrap/cache \
    && chown -R application:application storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 80

# Replace webdevops /entrypoint — we own boot (PORT, migrate, caches) + supervisord.
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
