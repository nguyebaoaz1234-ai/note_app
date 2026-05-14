#!/bin/bash

# 1. Khởi tạo Database
php artisan config:clear
php artisan migrate --force

# 2. Tiêu diệt triệt để MPM rác
rm -f /etc/apache2/mods-enabled/mpm_event.*
rm -f /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork || true

# 3. Bắt cổng động của Railway (Nếu không có thì dùng 8080)
TARGET_PORT=${PORT:-8080}

# 4. Đập đi xây mới toàn bộ cấu hình mạng của Apache
echo "ServerName localhost" >> /etc/apache2/apache2.conf
echo "Listen 0.0.0.0:$TARGET_PORT" > /etc/apache2/ports.conf

cat <<EOF > /etc/apache2/sites-available/000-default.conf
<VirtualHost *:$TARGET_PORT>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# 5. Khởi động máy chủ Apache
exec apache2-foreground