# Composer dependencies
FROM docker.io/library/php:8.4-cli-alpine AS vendor
WORKDIR /app

RUN apk add --no-cache git unzip
COPY --from=docker.io/composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
  --no-dev \
  --no-interaction \
  --prefer-dist \
  --optimize-autoloader \
  --no-scripts

# NodeJs + Vite + Tailwind build
FROM docker.io/node:22-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.* ./
COPY postcss.config.* ./
COPY tailwind.config.* ./
COPY tsconfig*.json ./
COPY resources/ resources/
COPY public/ public/

RUN npm run build

# Runtime
FROM docker.io/serversideup/php:8.4-fpm-nginx AS app
WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor/ /var/www/html/vendor/
COPY --from=assets /app/public/build/ /var/www/html/public/build/

USER root
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

USER www-data
RUN php artisan package:discover --ansi

EXPOSE 8080
