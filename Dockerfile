FROM php:8.2-fpm-alpine

# Cài đặt các dependency
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

# Set thư mục làm việc
WORKDIR /var/www

# Copy Composer vào container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy toàn bộ mã nguồn Laravel
COPY . .

# Cài đặt các gói phụ thuộc của Laravel
RUN composer install --no-interaction --optimize-autoloader

# Phân quyền cho thư mục storage và bootstrap/cache
RUN chmod -R 777 storage bootstrap/cache

# Mở cổng 80
EXPOSE 80

# Chạy php-fpm và nginx cùng lúc
CMD php artisan migrate --force && \
    php artisan db:seed --force && \
    php-fpm -D && \
    nginx -g "daemon off;"
