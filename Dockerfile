FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    bash \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    mysql-client \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .


RUN composer install || true

RUN chmod -R 777 storage bootstrap/cache

ENV PORT=80

EXPOSE 80

CMD php artisan migrate --force && \
    php artisan db:seed --force && \
    php-fpm -D
