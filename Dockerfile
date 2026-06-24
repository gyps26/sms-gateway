FROM php:8.4-fpm-alpine AS builder

RUN apk add --no-cache \
    curl zip unzip git libzip-dev libpng-dev libxml2-dev oniguruma-dev icu-dev \
    freetype-dev libjpeg-turbo-dev linux-headers openssl-dev gettext-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql mbstring exif pcntl bcmath gd intl zip soap gettext \
    && apk del $PHPIZE_DEPS

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --from=node:22-alpine /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node:22-alpine /usr/local/bin/node /usr/local/bin/
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

WORKDIR /var/www/html

COPY composer.json composer.lock package.json package-lock.json vite.config.js tailwind.config.js postcss.config.js ./
COPY resources resources/
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

RUN npm ci && npm run build && rm -rf node_modules

COPY . .

RUN php artisan storage:link --force 2>/dev/null || true
RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
    && chmod -R 775 storage bootstrap/cache

FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    nginx supervisor curl zip unzip libzip-dev libpng-dev libxml2-dev oniguruma-dev \
    icu-dev freetype-dev libjpeg-turbo-dev openssl-dev gettext-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql mbstring exif pcntl bcmath gd intl zip soap gettext \
    && apk del $PHPIZE_DEPS

COPY --from=builder /var/www/html /var/www/html

COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
