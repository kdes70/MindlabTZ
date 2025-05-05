FROM php:8.3-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Очистка кеша
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настройка рабочей директории
WORKDIR /var/www/html

# Копирование существующего приложения
COPY ./backend /var/www/html

# Установка прав на директории
RUN chown -R www-data:www-data /var/www/html

# Добавление пользователя хоста в контейнер
ARG HOST_UID=1000
ARG HOST_GID=1000
RUN groupmod -g ${HOST_GID} www-data && \
    usermod -u ${HOST_UID} -g ${HOST_GID} www-data && \
    chown -R www-data:www-data /var/www/html

# Запуск PHP-FPM
CMD ["php-fpm"]

EXPOSE 9000