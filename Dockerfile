# Sử dụng PHP 7.2
FROM php:7.2-apache

# Cập nhật đường dẫn tải phần mềm sang kho lưu trữ cũ (Sửa lỗi 404)
RUN echo "deb http://archive.debian.org/debian buster main" > /etc/apt/sources.list \
    && echo "deb http://archive.debian.org/debian-security buster/updates main" >> /etc/apt/sources.list \
    && echo "Acquire::Check-Valid-Until \"false\";" > /etc/apt/apt.conf.d/99no-check-valid-until

# Cài đặt các phần mềm phụ trợ
RUN apt-get update && apt-get install -y libpng-dev libzip-dev zip unzip git \
    && docker-php-ext-install pdo_mysql gd zip

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ code vào Container
COPY . /var/www/html

# Cài đặt thư viện bằng Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Tạo thư mục, liên kết ảnh và cấp quyền
RUN mkdir -p /var/www/html/public/uploads/avatars \
    && php artisan storage:link \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads

# =========================================================================
# TUYỆT CHIÊU: CHẠY BẰNG MÁY CHỦ CỦA LARAVEL ĐỂ NÉ HOÀN TOÀN LỖI APACHE
# Railway sẽ cấp một biến môi trường $PORT, ta ép Laravel chạy trên cổng đó
# =========================================================================
CMD php artisan serve --host=0.0.0.0 --port=$PORT