FROM php:8.2-apache

# Install PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite (useful for clean URLs later)
RUN a2enmod rewrite

# Set working directory inside container
WORKDIR /var/www/html