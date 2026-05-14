#!/bin/bash

# 1. Dọn dẹp cache và khởi tạo Database
php artisan config:clear
php artisan migrate --force

# 2. Tiêu diệt triệt để MPM rác
rm -f /etc/apache2/mods-enabled/mpm_event.*
rm -f /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork || true

# 3. Cấu hình cổng mạng linh hoạt theo Railway
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Bắt biến PORT từ Railway, nếu không có thì dùng tạm 8080
TARGET_PORT=${PORT:-8080}

echo "Listen 0.0.0.0:$TARGET_PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost .*/<VirtualHost *:$TARGET_PORT>/g" /etc/apache2/sites-available/000-default.conf

# 4. Khởi động máy chủ Apache
exec apache2-foreground