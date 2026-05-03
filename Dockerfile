```dockerfile
# Sử dụng đúng PHP 7.4 mượt mà nhất cho đồ án của em
FROM php:7.4-apache

# Cài đặt các phần mềm phụ trợ
RUN apt-get update && apt-get install -y libpng-dev libzip-dev zip unzip git
RUN docker-php-ext-install pdo_mysql gd zip

# Bật tính năng điều hướng của Apache
RUN a2enmod rewrite

# Ép máy chủ trỏ thẳng vào thư mục public (Bảo mật tuyệt đối, không cần .htaccess ngoài)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy toàn bộ code của em vào máy chủ
COPY . /var/www/html

# Tự động tải thư mục Vendor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Cấp quyền đọc ghi cho hệ thống
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache