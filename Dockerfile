# Nâng cấp lên PHP 7.4
FROM php:7.4-apache

# Cài đặt các phần mềm phụ trợ
RUN apt-get update && apt-get install -y libpng-dev libzip-dev zip unzip git \
    && docker-php-ext-install pdo_mysql gd zip

# Bật tính năng điều hướng
RUN a2enmod rewrite

# Tiêu diệt MPM thừa (Xóa sạch cả .load và .conf để tránh Crash)
RUN rm -f /etc/apache2/mods-enabled/mpm_event.* \
    && rm -f /etc/apache2/mods-enabled/mpm_worker.* \
    && a2enmod mpm_prefork

# ==============================================================================
# BÍ KÍP CHỐNG LẠC ĐƯỜNG: Ép Apache chạy đúng chuẩn IPv4 trên cổng 80
# ==============================================================================
RUN echo "Listen 0.0.0.0:80" > /etc/apache2/ports.conf
EXPOSE 80

# Thiết lập thư mục làm việc và Copy code
WORKDIR /var/www/html
COPY . /var/www/html

# Cài đặt thư viện bằng Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Phân quyền và tạo Storage Link
RUN mkdir -p /var/www/html/public/uploads/avatars \
    && php artisan storage:link \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads

# Ép máy chủ trỏ vào /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Kịch bản khởi động siêu tinh gọn
RUN echo '#!/bin/bash\n\
php artisan config:clear\n\
php artisan migrate --force\n\
exec apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]