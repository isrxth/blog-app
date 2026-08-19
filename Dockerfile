FROM php:8.2-fpm-alpine

# Install Nginx and MySQL drivers
RUN apk add --no-cache nginx \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Ensure Nginx runs as www-data to match PHP-FPM
RUN sed -i 's/user nginx;/user www-data;/g' /etc/nginx/nginx.conf

# Configure Nginx virtual host
RUN mkdir -p /run/nginx /var/www/html
COPY <<EOF /etc/nginx/http.d/default.conf
server {
    listen 80;
    server_name _;
    root /var/www/html;
    index index.php index.html;

    # Static assets handling with explicit permissions
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        try_files \$uri =404;
        access_log off;
        expires max;
    }

    # Standard routing
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP-FPM processing
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny hidden files like .env or .git
    location ~ /\. {
        deny all;
    }
}
EOF

# Copy source files
COPY src/ /var/www/html/

# Ensure both PHP-FPM and Nginx have full read/execute traversal permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} +

WORKDIR /var/www/html
EXPOSE 80

# Start both PHP-FPM and Nginx
CMD php-fpm -D && nginx -g 'daemon off;'