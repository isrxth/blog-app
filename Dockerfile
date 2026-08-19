FROM php:8.2-apache

# Install MySQL drivers
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Ensure Apache rewrite is enabled and only prefork MPM is loaded
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

# Enable .htaccess support in /var/www/
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy source code into the web root
COPY src/ /var/www/html/

# Ensure file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]