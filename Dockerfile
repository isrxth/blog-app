FROM php:8.2-apache

# Install PDO MySQL and MySQLi drivers
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy only the source code directly into Apache's web root
COPY src/ /var/www/html/

# Set appropriate permissions for Apache
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]