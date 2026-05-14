# Nâng cấp lên PHP 7.4
FROM php:7.4-apache

# Cài đặt các phần mềm phụ trợ & Database
RUN apt-get update && apt-get install -y libpng-dev libzip-dev zip unzip git \
    && docker-php-ext-install pdo_mysql gd zip

# Bật tính năng điều hướng (Rewrite) cho Laravel
RUN a2enmod rewrite

# Thiết lập thư mục làm việc và Copy code vào
WORKDIR /var/www/html
COPY . /var/www/html

# Cài đặt thư viện bằng Composer (PHỚT LỜ KIỂM TRA PHIÊN BẢN PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

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
# COMBO CHỐT HẠ: Xóa MPM thừa ngay lúc Khởi động (Runtime) -> Bật Web
# ==============================================================================
CMD php artisan config:clear ; \
    php artisan migrate --force ; \
    rm -f /etc/apache2/mods-enabled/mpm_event.conf ; \
    rm -f /etc/apache2/mods-enabled/mpm_event.load ; \
    rm -f /etc/apache2/mods-enabled/mpm_worker.conf ; \
    rm -f /etc/apache2/mods-enabled/mpm_worker.load ; \
    a2enmod mpm_prefork ; \
    sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf ; \
    sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf ; \
    apache2-foreground