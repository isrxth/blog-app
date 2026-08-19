FROM php:8.2-apache

# Install PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Explicitly grant access to /var/www/html in Apache config
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && echo '<Directory /var/www/html/>\n    Options -Indexes +FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>' > /etc/apache2/conf-available/allow-all.conf \
    && a2enconf allow-all

# Copy source code
COPY src/ /var/www/html/

# Apply proper ownership and execute/read permissions across directories and files
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} +

WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]