FROM php:8.2-apache

# Install PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Ensure Apache rewrite is enabled and only mpm_prefork is active
RUN a2enmod rewrite \
    && a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork

# Copy application files
COPY src/ /var/www/html/

# Set proper ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]