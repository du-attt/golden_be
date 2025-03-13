FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
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


WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install || true


RUN chmod -R 777 storage bootstrap/cache

EXPOSE ${PORT}
RUN cp .env.example .env 
CMD php artisan key:generate && \
    php artisan config:cache && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php-fpm