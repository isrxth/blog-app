FROM php:8.2-fpm-alpine

# Install Nginx and MySQL drivers
RUN apk add --no-cache nginx \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Configure Nginx
RUN mkdir -p /run/nginx /var/www/html
COPY <<EOF /etc/nginx/http.d/default.conf
server {
    listen 80;
    server_name _;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
EOF

# Copy source files
COPY src/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html
EXPOSE 80

# Start both PHP-FPM and Nginx
CMD php-fpm -D && nginx -g 'daemon off;'