FROM php:8.3-fpm-alpine AS builder

# Устанавливаем зависимости для сборки
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    autoconf \
    make \
    gcc \
    g++ \
    libc-dev \
    linux-headers \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev

# Устанавливаем расширения PHP
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    opcache && \
    pecl install redis xdebug && \
    docker-php-ext-enable redis xdebug

FROM php:8.3-fpm-alpine

# Устанавливаем runtime зависимости
RUN apk add --no-cache \
    libpng \
    libjpeg-turbo \
    libwebp \
    shadow \
    acl

ARG HOST_UID=1000
ARG HOST_GID=1000

# Настраиваем пользователя и группу
RUN set -eux; \
    deluser www-data; \
    addgroup -g $HOST_GID -S www-data; \
    adduser -u $HOST_UID -S -D -G www-data www-data; \
    install -d -m 0755 -o www-data -g www-data /home/www-data

# Копируем расширения из builder
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/bin/php /usr/local/bin/php
COPY --from=builder /usr/local/sbin/php-fpm /usr/local/sbin/php-fpm

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем приложение и настраиваем права
COPY --chown=www-data:www-data ./backend .

# Создаем необходимые директории и настраиваем права
RUN set -eux; \
    mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache; \
    chmod -R 775 storage bootstrap/cache; \
    chown -R www-data:www-data storage bootstrap/cache; \
    composer dump-autoload

USER www-data

CMD ["php-fpm"]