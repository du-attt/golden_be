# Sử dụng PHP-FPM Alpine để nhẹ hơn
FROM php:8.2-fpm-alpine

# Cài đặt các thư viện cần thiết
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

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Chọn thư mục làm việc
WORKDIR /var/www

# Copy toàn bộ mã nguồn Laravel vào Docker
COPY . .

# Cài đặt các package của Laravel
RUN composer install || true

# Phân quyền cho các thư mục lưu trữ
RUN chmod -R 777 storage bootstrap/cache

# Expose port (dành cho Render)
ENV PORT=80

# Cài đặt Nginx trực tiếp trong Docker
RUN mkdir -p /var/run/nginx && \
    echo "server {
        listen ${PORT};
        root /var/www/public;
        index index.php index.html index.htm;

        location / {
            try_files \$uri \$uri/ /index.php?\$query_string;
        }

        location ~ \.php\$ {
            include fastcgi_params;
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        }

        location ~ /\.ht {
            deny all;
        }
    }" > /etc/nginx/conf.d/default.conf

# Mở cổng 80
EXPOSE 80

# Chạy Laravel migration, seed, rồi chạy PHP-FPM và Nginx
CMD php artisan migrate --force && \
    php artisan db:seed --force && \
    php-fpm -D && \
    nginx -g "daemon off;"
