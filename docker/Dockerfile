FROM php:8.4-fpm

# System deps
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git curl unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libzip-dev libonig-dev libxml2-dev libicu-dev \
        sqlite3 libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql pdo_sqlite mbstring zip exif pcntl intl gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Use a non-root user for runtime
RUN useradd -u 1000 -m appuser
USER appuser
