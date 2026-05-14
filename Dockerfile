# Sử dụng PHP 7.2 bản ổn định nhất
FROM php:7.2-apache

# Cập nhật đường dẫn tải phần mềm sang kho lưu trữ cũ
RUN echo "deb http://archive.debian.org/debian buster main" > /etc/apt/sources.list \
    && echo "deb http://archive.debian.org/debian-security buster/updates main" >> /etc/apt/sources.list \
    && echo "Acquire::Check-Valid-Until \"false\";" > /etc/apt/apt.conf.d/99no-check-valid-until

# Cài đặt các phần mềm phụ trợ & Database
RUN apt-get update && apt-get install -y libpng-dev libzip-dev zip unzip git \
    && docker-php-ext-install pdo_mysql gd zip

# Bật tính năng điều hướng (Rewrite) cho Laravel
RUN a2enmod rewrite

# Thiết lập thư mục làm việc và Copy code vào
WORKDIR /var/www/html
COPY . /var/www/html

# Cài đặt thư viện bằng Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Tạo thư mục ảnh, nối Link Storage và cấp quyền đọc ghi
RUN mkdir -p /var/www/html/public/uploads/avatars \
    && php artisan storage:link \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads

# Ép máy chủ trỏ thẳng vào thư mục /public để bảo mật
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Báo cho hệ thống biết web chạy ở cổng 80
EXPOSE 80

# ==============================================================================
# COMBO CHỐT HẠ: Xóa Cache cũ -> Tạo Bảng DB -> Mở Cổng Railway -> Bật Web
# ==============================================================================
CMD php artisan config:clear \
    && php artisan migrate --force \
    && sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf \
    && sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf \
    && apache2-foreground