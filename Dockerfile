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

RUN echo "daemon off;" >> /usr/local/etc/php-fpm.conf

RUN chmod -R 777 storage bootstrap/cache

# Tạo file .env từ .env.example
RUN cp .env.example .env 

# Generate app key và cache config
RUN php artisan key:generate && \
    php artisan config:cache && \
    php artisan migrate --force && \
    php artisan db:seed --force

EXPOSE ${PORT}
ENV PORT=${PORT}

CMD ["php-fpm"]
