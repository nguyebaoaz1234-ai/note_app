# Nâng cấp lên PHP 7.4
FROM php:7.4-apache

# Cài đặt các phần mềm phụ trợ
RUN apt-get update && apt-get install -y libpng-dev libzip-dev zip unzip git \
    && docker-php-ext-install pdo_mysql gd zip

# Bật tính năng điều hướng
RUN a2enmod rewrite

# Sửa lỗi cảnh báo ServerName của Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

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

# ==============================================================================
# BÍ KÍP CHỐNG LẠC ĐƯỜNG: Ghi đè tuyệt đối cổng mạng để khớp 100% với Railway
# ==============================================================================
RUN echo '#!/bin/bash\n\
php artisan config:clear\n\
php artisan migrate --force\n\
rm -f /etc/apache2/mods-enabled/mpm_event.*\n\
rm -f /etc/apache2/mods-enabled/mpm_worker.*\n\
a2enmod mpm_prefork || true\n\
echo "Listen 0.0.0.0:${PORT:-80}" > /etc/apache2/ports.conf\n\
sed -i "s/<VirtualHost .*/<VirtualHost \*:${PORT:-80}>/g" /etc/apache2/sites-available/000-default.conf\n\
exec apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]